<template>
  <div class="platby">
    <div class="table">
      <div class="thead grid-platby">
        <div class="th th-index"></div>
        <div class="th th-summary">
          {{ summaryText }}
        </div>
        <div class="th th-carry">Preplatok z minulého mesiaca</div>
        <div class="th th-prikaz">
          <span>Platobný príkaz</span>
          <button
            class="button"
            @click="sendAll"
            :disabled="sendingAll || rows.every((r) => isRequestSent(r))"
          >
            Odoslať
          </button>
        </div>
        <div class="th th-spacer"></div>
        <div class="th th-abs">Absolvované lekcie</div>
        <div class="th th-inv">Neabsolvované lekcie</div>
        <div class="th th-canc">Zrušené lekcie</div>
        <div class="th th-paid">Uhradený mesiac</div>
      </div>

      <div v-if="rows.length === 0" class="empty-state">
        Na tento deň nie sú žiadne lekcie.
      </div>

      <div
        v-for="(r, i) in rows"
        :key="r.id"
        class="platba-row grid-platby"
        :class="{ 'row-alt': i % 2 === 1, active: activeRow === r.id }"
        @click="toggleRow(r.id)"
        @mouseenter="hoveredRow = r.id"
        @mouseleave="hoveredRow = null"
      >
        <div class="cell col-index">{{ i + 1 }}.</div>

        <div class="cell col-student">
          <div class="student-card" :class="r.mode === 'duo' ? 'duo' : 'solo'">
            <div class="avatar-wrap">
              <img class="avatar" :src="r.avatar" alt="" />
            </div>
            <div class="meta">
              <div class="name">{{ r.name }}</div>
              <div class="time">Začiatok hodiny:&nbsp; {{ r.time }}</div>
            </div>
          </div>
        </div>

        <div class="cell col-carry">
          <span class="currency">{{ money(r.carryOver) }}</span>
        </div>

        <div class="cell col-prikaz" @click.stop>
          <select
            class="native select-pill"
            :class="r.mode === 'duo' ? 'duo' : 'solo'"
            v-model.number="r.fee"
            @change="onFeeChange(r)"
            :data-payment-id="r.payment_id || ''"
          >
            <option
              v-for="opt in feeOptions(r)"
              :key="opt.value"
              :value="opt.value"
            >
              {{ opt.label }}
            </option>
          </select>

          <button
            class="icon-square icon-mail"
            :class="{ 'disabled-mail': isRequestSent(r) }"
            :disabled="isRequestSent(r)"
            @click="!isRequestSent(r) ? send(r) : null"
            aria-label="Odoslať"
          >
            <img src="@/assets/images/icons/odoslatMail.svg" alt="" />
          </button>
        </div>

        <div class="cell col-spacer" @click.stop>
          <button
            v-if="
              ((activeRow === r.id || hoveredRow === r.id) &&
                isRequestSent(r)) ||
              isNoticeSent(r)
            "
            class="icon-square icon-bell"
            :class="{ 'disabled-mail': isNoticeSent(r) }"
            :disabled="isNoticeSent(r)"
            aria-label="Pripomenúť nezaplatené"
            @click="!isNoticeSent(r) ? remind(r) : null"
          >
            <img src="@/assets/images/icons/neuhradenaPlatba.svg" alt="" />
          </button>
        </div>

        <div class="cell col-lekcie">
          <div class="bar bar-wide" :class="r.mode === 'duo' ? 'duo' : 'solo'">
            <span class="num">{{ r.countAbsolvedLessons }}</span>
            <span class="num">{{ r.countParentCancelledLesson }}</span>
            <span class="num">{{ r.countAdminCancelledLesson }}</span>
          </div>
        </div>

        <div class="cell col-paid">
          <button
            class="pill paid"
            :class="{ off: !r.paid, glow: Number(r.paidSum) === 0 }"
            :disabled="isPaidLocked(r)"
            @click.stop="handlePaidClick(r)"
          >
            <img src="@/assets/images/icons/uhradenyMesiac.svg" alt="" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AdminPlatobnePrikazy",
  mounted() {
    this.testApi();
  },

  watch: {
    day(val, oldVal) {
      console.log("=== WATCHER DAY ===", {
        oldVal,
        val,
        typeofOld: typeof oldVal,
        typeofVal: typeof val,
      });
      this.testApi();
    },
    month(val, oldVal) {
      console.log("Watcher: month changed", oldVal, "→", val);
      this.testApi();
    },
    year(val, oldVal) {
      console.log("Watcher: year changed", oldVal, "→", val);
      this.testApi();
    },
    teacherId(val, oldVal) {
      console.log("Watcher: teacherId changed", oldVal, "→", val);
      this.testApi();
    },
  },
  props: {
    day: {
      type: [Number, String],
      required: true,
    },
    month: {
      type: Number,
      required: true,
    },
    year: {
      type: Number,
      required: true,
    },
    teacherId: {
      type: String,
      required: true,
    },
  },
  data() {
    // removed unused iso function
    return {
      lessonsPerMonth: 4,
      remindBeforeDays: 3,
      remindAfterDays: 1,
      activeRow: null,
      hoveredRow: null,
      sendingAll: false,
      rows: [],
      // Pozn.: Každý riadok (student) môže niesť: payment_id, qr
      apiTestResult: "",
      apiLessonCount: 0,
    };
  },
  computed: {
    monthName() {
      const months = [
        "Január",
        "Február",
        "Marec",
        "Apríl",
        "Máj",
        "Jún",
        "Júl",
        "August",
        "September",
        "Október",
        "November",
        "December",
      ];
      return `${months[this.month - 1]} ${this.year}`;
    },
    totalFee() {
      return this.rows.reduce((s, r) => s + Number(r.fee || 0), 0);
    },
    summaryText() {
      const soloSum = this.apiLessonCount * 16;
      const duoSum = this.apiLessonCount * 14;
      return `V mesiaci ${this.monthName} sú ${this.apiLessonCount} lekcie (solo: ${soloSum}€ / duo: ${duoSum}€)`;
    },
  },
  methods: {
    money(v) {
      return `${Number(v).toFixed(0)}€`;
    },
    feeOptions(r) {
      const unit = r.mode === "duo" ? 14 : 16; // základná cena za lekciu
      const max = 8; // do 8-násobku
      const opts = [];
      for (let k = 1; k <= max; k++) {
        const val = unit * k;
        opts.push({ value: val, label: `${val}€` });
      }
      // ak má r.fee nejakú inú hodnotu (napr. manuálne prepočítanú), nech je dostupná
      const current = Number(r.fee || 0);
      if (current && !opts.some((o) => o.value === current)) {
        opts.push({ value: current, label: `${current}€` });
      }
      return opts;
    },
    async sendAll() {
      if (this.sendingAll) return;
      this.sendingAll = true;
      try {
        const targets = this.rows.filter((r) => !this.isRequestSent(r));
        if (targets.length === 0) {
          this.apiTestResult = "Všetky platobné príkazy už boli odoslané.";
          return;
        }

        let sent = 0;
        for (const r of targets) {
          await this.send(r);
          sent++;
          // krátka pauza, aby sme nezaťažili server a mali stabilnejšie odosielanie
          await new Promise((res) => setTimeout(res, 250));
        }
        this.apiTestResult = `Hromadné odoslanie hotové: ${sent}/${targets.length} adresátom.`;
      } catch (e) {
        this.apiTestResult = `Chyba pri hromadnom odosielaní: ${e}`;
      } finally {
        this.sendingAll = false;
      }
    },

    async send(r) {
      try {
        r.__sendingRequest = true;
        // 1) Najprv si vždy zisti / vytvor platbu
        const iq = await this.inquirePayment(r.id);

        if (!iq.ok || !iq.payment_id) {
          r.__sendingRequest = false;
          this.apiTestResult =
            `Nepodarilo sa získať payment_id pre používateľa ${r.name}. ` +
            (iq && iq.message
              ? `Dôvod: ${iq.message}`
              : "Skontroluj backend inquirePayment.php, aby vracal 'payment_id'.");
          return;
        }

        await this.savePayment(r);

        // 2) Následne odošli mail s payment_id
        const body = new URLSearchParams();
        body.append("student_id", r.id);
        body.append("payment_id", String(iq.payment_id));

        const res = await fetch(
          "/api/edupage/sendMailEdupagePaymentRequest.php",
          {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
            },
            body,
          }
        );

        const json = await res.json();
        const ok =
          json &&
          typeof json.status === "string" &&
          json.status.toLowerCase().includes("succesfull");

        if (ok) {
          this.apiTestResult = `Platobný príkaz (ID ${iq.payment_id}) úspešne odoslaný používateľovi ${r.name}.`;
          // voliteľne si poznač do riadku, aby sa deaktivovalo tlačidlo
          r.email_payment_request = "true";
          // r.__sendingRequest = false;
          await this.savePayment(r, { email_payment_request: "true" });

          // auto-activate row so že zvonček je hneď viditeľný
          this.activeRow = r.id;
        } else {
          r.__sendingRequest = false;
          this.apiTestResult =
            `Chyba pri odosielaní platobného príkazu používateľovi ${r.name}: ` +
            (json && (json.message || json.data)
              ? json.message || json.data
              : "Neznáma chyba");
        }
      } catch (e) {
        r.__sendingRequest = false;
        this.apiTestResult =
          `Chyba pri odosielaní platobného príkazu používateľovi ${r.name}: ` +
          e;
      }
    },

    async inquirePayment(userId) {
      try {
        const qs = new URLSearchParams({
          id: String(userId),
          month: String(this.month),
          year: String(this.year),
        });
        const res = await fetch(
          `/api/edupage/inquirePayment.php?${qs.toString()}`
        );
        const json = await res.json();

        // Podpora rôznych tvarov odpovede (json.status/json.data atď.)
        const container = json && (json.data || json);
        const payment_id =
          container &&
          (container.payment_id ||
            container.paymentId ||
            (container.payment && container.payment.id) ||
            container.id); // BE momentálne vracia { data: { id: "29", ... } }
        const lesson =
          (container &&
            (container.lesson ||
              (container.payment && container.payment.lesson))) ||
          "";
        const date =
          (container &&
            (container.date ||
              (container.payment && container.payment.date))) ||
          "";
        const qr =
          (container &&
            (container.qr || (container.payment && container.payment.qr))) ||
          "";
        const message = (json && (json.message || json.status)) || "";

        // voliteľne vypíš pre debug
        this.apiTestResult = `InquirePayment odpoveď: ${JSON.stringify(
          json,
          null,
          2
        )}`;

        return {
          ok: Boolean(payment_id),
          payment_id: payment_id ? String(payment_id) : null,
          lesson: String(lesson),
          date: String(date),
          qr: String(qr),
          raw: json,
          message,
        };
      } catch (e) {
        return { ok: false, payment_id: null, raw: null, message: String(e) };
      }
    },
    isRequestSent(r) {
      return (
        r &&
        r.email_payment_request !== undefined &&
        r.email_payment_request !== null &&
        r.email_payment_request !== "Payment not found"
      );
    },
    isNoticeSent(r) {
      return (
        r &&
        r.email_payment_notice !== undefined &&
        r.email_payment_notice !== null &&
        r.email_payment_notice !== "Payment not found"
      );
    },
    async remind(r) {
      try {
        // 1) zisti/vytvor platbu ako pri requeste
        const iq = await this.inquirePayment(r.id);
        if (!iq.ok || !iq.payment_id) {
          this.apiTestResult =
            `Nepodarilo sa získať payment_id pre používateľa ${r.name}. ` +
            (iq && iq.message
              ? `Dôvod: ${iq.message}`
              : "Skontroluj backend inquirePayment.php, aby vracal 'payment_id'.");
          return;
        }

        // 2) odošli NOTICE mail (rovnaký endpoint, ale s typom notice)
        const body = new URLSearchParams();
        body.append("student_id", r.id);
        body.append("payment_id", String(iq.payment_id));

        const res = await fetch(
          "/api/edupage/sendMailEdupagePaymentNotice.php",
          {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
            },
            body,
          }
        );

        const json = await res.json();
        const ok =
          json &&
          typeof json.status === "string" &&
          json.status.toLowerCase().includes("succesfull");

        if (ok) {
          this.apiTestResult = `Platobná PRIPOMIENKA (notice) pre payment ID ${iq.payment_id} úspešne odoslaná používateľovi ${r.name}.`;
          r.email_payment_notice = "true";
          await this.savePayment(r, { email_payment_notice: "true" });
        } else {
          this.apiTestResult =
            `Chyba pri odoslaní pripomienky používateľovi ${r.name}: ` +
            (json && (json.message || json.data)
              ? json.message || json.data
              : "Neznáma chyba");
        }
      } catch (e) {
        this.apiTestResult =
          `Chyba pri odoslaní pripomienky používateľovi ${r.name}: ` + e;
      }
    },
    isPaidLocked(r) {
      const fee = Number(r.fee || 0);
      const sum = Number(r.paidSum || 0);
      if (!Number.isFinite(fee) || !Number.isFinite(sum)) return false;
      return sum >= fee; // zamkne, keď je už uhradené
    },

    handlePaidClick(r) {
      if (this.isPaidLocked(r)) return; // nič neurob, je zamknuté
      this.togglePaid(r);
      this.savePayment(r, { __markPaid: 1 });
    },

    togglePaid(r) {
      r.paid = !r.paid;
      // Ak BE očakáva, že pri uložení sa cena z price (fee) zapíse do paid, necháme BE spraviť logiku.
      // Pre UX si lokálne nastavíme paidSum na úroň fee, ak sa označí ako zaplatené.
      if (r.paid) {
        r.paidSum = Number(r.fee || 0);
      }
    },

    async onFeeChange(r) {
      // pri zmene ceny automaticky ulož na BE
      await this.savePayment(r);
    },

    async ensurePaymentId(r) {
      // ak už máme payment_id, použime ho; inak si ho vyžiadame cez inquirePayment
      if (r.payment_id) return r.payment_id;
      const iq = await this.inquirePayment(r.id);
      if (iq && iq.ok && iq.payment_id) {
        r.payment_id = iq.payment_id;
        return iq.payment_id;
      }
      return null;
    },

    async savePayment(r, extra = {}) {
      try {
        const paymentId = await this.ensurePaymentId(r);
        if (!paymentId) {
          this.apiTestResult = `Nepodarilo sa získať payment_id pre ${r.name}. Uloženie zrušené.`;
          return;
        }
        // doplň lesson/date/qr ak chýbajú
        if (!r.lesson || !r.date || !r.qr) {
          const iq = await this.inquirePayment(r.id);
          if (iq && iq.ok) {
            // ak stále nemáme lesson a máme lesson_ids → poskladaj JSON
            if (
              (!r.lesson || r.lesson === "") &&
              Array.isArray(r.lesson_ids) &&
              r.lesson_ids.length
            ) {
              r.lesson = JSON.stringify(r.lesson_ids.map(String));
            }

            // ak nemáme date → dnešný dátum dd.mm.YYYY
            if (!r.date || r.date === "") {
              const now = new Date();
              const dd = String(now.getDate()).padStart(2, "0");
              const mm = String(now.getMonth() + 1).padStart(2, "0");
              const yyyy = String(now.getFullYear());
              r.date = `${dd}.${mm}.${yyyy}`;
            }
            if (!r.qr && iq.qr) r.qr = iq.qr;
          }
        }
        const body = new URLSearchParams();
        body.append("id", String(paymentId)); // * required (payment id)
        body.append("user_id", String(r.id));
        // Neposielame lesson/date ak ich nemáme k dispozícii; BE môže zanechať bez zmeny
        if (r.fee !== undefined && r.fee !== null) {
          const feeNum = Number(r.fee);
          const feeStr = Number.isFinite(feeNum) ? feeNum.toFixed(2) : "0.00";
          body.append("price", feeStr);
        }
        // status sa posiela podľa aktuálneho stavu r.paid
        const status = r.paid ? "PAID" : "PENDING";
        body.append("status", status);
        // posielaj paid ako double s dvoma desatinnými miestami
        if (r.paid) {
          const feeNum = Number(r.fee);
          const paidNum = Number(r.paidSum);
          const valueToSend = extra.__markPaid
            ? Number.isFinite(feeNum)
              ? feeNum.toFixed(2)
              : "0.00"
            : Number.isFinite(paidNum)
            ? paidNum.toFixed(2)
            : Number.isFinite(feeNum)
            ? feeNum.toFixed(2)
            : "0.00";
          body.append("paid", valueToSend);
        } else {
          body.append("paid", "0.00");
        }

        body.append("lesson", r.lesson != null ? String(r.lesson) : "");
        body.append("date", r.date != null ? String(r.date) : "");
        // Ensure QR `price` matches the current fee with two decimals — keep other params intact (no re-encoding)
        {
          const feeNum = Number(r.fee);
          const feeStr = Number.isFinite(feeNum) ? feeNum.toFixed(2) : "0.00"; // always like 64.00
          let qrOut = r.qr != null ? String(r.qr) : "";
          if (qrOut) {
            // Replace only the price param value; do not touch other params (preserve diacritics as-is)
            const hasPrice = /([?&])price=([^&]*)/i.test(qrOut);
            if (hasPrice) {
              qrOut = qrOut.replace(
                /([?&])price=([^&]*)/i,
                `$1price=${feeStr}`
              );
            } else {
              const join = qrOut.includes("?") ? "&" : "?";
              qrOut = `${qrOut}${join}price=${feeStr}`;
            }
          }
          body.append("qr", qrOut);
        }
        // flagy pre emaily (vždy pošli kľúč, aj keď je prázdny)
        body.append(
          "email_payment_request",
          r.email_payment_request != null
            ? String(r.email_payment_request)
            : "Payment not found"
        );
        body.append(
          "email_payment_notice",
          r.email_payment_notice != null
            ? String(r.email_payment_notice)
            : "Payment not found"
        );
        // extra prepísania
        for (const [k, v] of Object.entries(extra)) {
          if (v !== undefined && v !== null) body.set(k, String(v));
        }

        const res = await fetch("/api/edupage/editPayment.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
          },
          body,
        });
        const json = await res.json().catch(() => null);
        const ok =
          json &&
          typeof json.status === "string" &&
          json.status.toLowerCase().includes("succesfull");
        if (ok) {
          this.apiTestResult = `Platba pre ${r.name} bola uložená.`;
          // zosúladíme paidSum s fee IBA ak sme práve explicitne označili ako zaplatené
          if (extra && extra.__markPaid) {
            r.paidSum = Number(r.fee || 0);
          }
          if (["true", true, 1, "1"].includes(extra.email_payment_notice)) {
            r.email_payment_notice = "true";
          }
          if (["true", true, 1, "1"].includes(extra.email_payment_request)) {
            r.email_payment_request = "true";
          }
        } else {
          this.apiTestResult =
            `Uloženie platby pre ${r.name} zlyhalo.` +
            (json && (json.message || json.data)
              ? ` Dôvod: ${json.message || json.data}`
              : "");
        }
      } catch (e) {
        this.apiTestResult = `Chyba pri ukladaní platby pre ${r.name}: ${e}`;
      }
    },

    toggleRow(id) {
      this.activeRow = this.activeRow === id ? null : id;
    },
    async testApi() {
      console.log("testApi called with", {
        day: this.day,
        month: this.month,
        year: this.year,
        teacherId: this.teacherId,
      });
      // day: 0=nedeľa, 1=pondelok, ...
      // Map slovných dní na čísla (0=nedeľa, 1=pondelok, ...)
      const dayMap = {
        nedeľa: 7,
        nedela: 7,
        pondelok: 1,
        utorok: 2,
        streda: 3,
        štvrtok: 4,
        stvrtok: 4,
        piatok: 5,
        sobota: 6,
      };
      let dayNum = this.day;
      if (typeof dayNum === "string") {
        const d = dayNum
          .toLowerCase()
          .normalize("NFD")
          .replace(/\p{Diacritic}/gu, "");
        dayNum = dayMap[d] !== undefined ? dayMap[d] : "";
      }
      if (typeof dayNum === "number" && isNaN(dayNum)) dayNum = "";
      const url = `/api/edupage/listLessonsByTeacher.php?teacher_id=${
        this.teacherId
      }&year=${this.year}&month=${String(this.month).padStart(
        2,
        "0"
      )}&day=${dayNum}`;
      try {
        const res = await fetch(url);
        const json = await res.json();
        this.apiTestResult = JSON.stringify(json, null, 2);
        console.log("API result:", json);

        if (json && json.data) {
          this.apiLessonCount = Number(json.data.count) || 0;
        }

        if (json && json.data && Array.isArray(json.data.student_data)) {
          // Persist "sent" flags locally to keep mail button disabled after sending, even if API does not yet reflect it
          const prevReq = Object.create(null);
          const prevNotice = Object.create(null);
          for (const row of this.rows || []) {
            if (row && row.id != null) {
              if (row.email_payment_request === "true") prevReq[row.id] = true;
              if (row.email_payment_notice === "true")
                prevNotice[row.id] = true;
            }
          }
          this.rows = json.data.student_data.map((s) => {
            const name = (s.name || "") + (s.surname ? " " + s.surname : "");
            let avatar = s.profilePhotoUrl || "https://via.placeholder.com/56";
            if (typeof avatar === "string") {
              avatar = avatar
                .trim()
                .replace(
                  "www.heligonkovaakademia.sk",
                  "heligonkovaakademia.sk"
                );
            }
            let time = "";
            if (Array.isArray(s.lesson_starts) && s.lesson_starts.length > 0) {
              const last = s.lesson_starts[s.lesson_starts.length - 1];
              if (last && !isNaN(Number(last))) {
                const start = Number(last);
                const h = Math.floor(start / 100);
                const m = start % 100;
                let len = s.formOfStudy === "duo" ? 45 : 30;
                const endM = m + len;
                const endH = h + Math.floor(endM / 60);
                const endMin = endM % 60;
                time =
                  `${String(h).padStart(2, "0")}:${String(m).padStart(
                    2,
                    "0"
                  )}` +
                  " – " +
                  `${String(endH).padStart(2, "0")}:${String(endMin).padStart(
                    2,
                    "0"
                  )}`;
              }
            }
            let fee = 0;
            if (s.price && !isNaN(Number(s.price))) {
              fee = Number(s.price);
            } else {
              // automaticky vypočítaj podľa pravidiel
              const count =
                (this.apiLessonCount || 0) - (s.countAdminCancelledLesson || 0);
              if (s.formOfStudy === "duo") {
                fee = count * 14;
              } else {
                fee = count * 16;
              }
            }
            // priamo hodnoty z API
            const paidSum = Number(
              s.paid ??
                s.paid_sum ??
                s.paidSum ??
                s.paid_total ??
                s.sumPaid ??
                0
            );
            const paid = paidSum >= Number(fee || 0);
            // normalize email flags from API, but persist local "sent" state if set
            const normFlag = (v) =>
              v === true ||
              v === 1 ||
              v === "1" ||
              (typeof v === "string" && v.toLowerCase() === "true")
                ? "true"
                : null;
            const email_payment_request =
              normFlag(s.email_payment_request) ||
              (prevReq[s.id] ? "true" : null);
            const email_payment_notice =
              normFlag(s.email_payment_notice) ||
              (prevNotice[s.id] ? "true" : null);
            // za email_payment_notice:
            const lessonIds = Array.isArray(s.lesson_ids)
              ? s.lesson_ids.filter((x) => x != null).map(String)
              : [];

            return {
              id: s.id,
              mode: s.formOfStudy === "duo" ? "duo" : "solo",
              name,
              avatar,
              time,
              carryOver: 0,
              fee,
              countAbsolvedLessons: s.countAbsolvedLessons || 0,
              countAdminCancelledLesson: s.countAdminCancelledLesson || 0,
              countParentCancelledLesson: s.countParentCancelledLesson || 0,
              paid,
              paidSum,
              dueISO: null,
              email_payment_request,
              email_payment_notice,
              lesson_ids: lessonIds,
              lesson: JSON.stringify(lessonIds), // toto je to, čo pôjde do BE
              date: s.date ?? "",
              payment_id: s.payment_id || null,
              qr: s.qr || null,
            };
          });
        } else {
          this.rows = [];
        }
      } catch (e) {
        this.apiTestResult = "Chyba pri načítaní: " + e;
      }
    },
  },
};
</script>

<style scoped lang="scss">
.platby {
  --gap: 0.875em;
  --control-h: 2em;
  --radius: 0.9em;
  --green: #90ca50;
  --green-dark: #00913f;
  --yellow: #fef35a;
  --ink: #000;
  --ink-44: rgba(0, 0, 0, 0.44);
  --ink-50: rgba(0, 0, 0, 0.5);
  inline-size: 100%;
  overflow-x: hidden;
  font-size: 80%;
}

.table {
  inline-size: 100%;
  max-inline-size: 70em;
  margin-inline: auto;
  padding-inline: 1em;
  margin-block: 3em;
  font-size: 1em;
}

.grid-platby {
  display: grid;
  align-items: center;
  column-gap: clamp(0.5em, 1.2vw, 0.75em);
  grid-template-columns:
    [idx] 3ch
    [student] minmax(18em, 1.6fr)
    [carry] clamp(8em, 7vw, 8em)
    [fee] minmax(10em, 0.75fr)
    [spacer] clamp(2em, 4vw, 3em)
    [lessA] minmax(4.5em, 0.65fr)
    [lessB] minmax(4.5em, 0.6fr)
    [lessC] minmax(3.5em, 0.4fr)
    [paid] clamp(3.6em, 5vw, 5.2em);
}

.thead .th {
  font-size: 0.875em;
}
.thead .th-summary {
  font-size: 1em;
  font-weight: 700;
}

.th-index,
.col-index {
  grid-column: idx;
}
.th-summary,
.col-student {
  grid-column: student;
}
.th-carry,
.col-carry {
  grid-column: carry;
}
.th-prikaz,
.col-prikaz {
  grid-column: fee;
}
.th-spacer,
.col-spacer {
  grid-column: spacer;
}
.th-abs {
  grid-column: lessA;
}
.th-inv {
  grid-column: lessB;
}
.th-canc {
  grid-column: lessC;
}
.th-paid,
.col-paid {
  grid-column: paid;
}
.col-lekcie {
  grid-column: lessA / span 3;
}

.platba-row {
  padding-block: 0.625em;
}
.platba-row.active,
.platba-row:hover {
  background: #d9d9d9;
}

.col-index {
  font-size: 2em;
  font-weight: 700;
  color: var(--ink-44);
  text-align: center;
}

.col-student {
  display: flex;
  align-items: center;
  gap: 0.625em;
  min-width: 0;
}
.col-student .student-card {
  display: flex;
  align-items: center;
  gap: 1em;
  inline-size: 100%;
  min-width: 0;
  background: var(--green);
  border-radius: 0.75em;
  padding: 0.75em 1em;
  box-shadow: 0 0.75em 1.25em rgba(0, 0, 0, 0.16),
    inset 0 -0.2em 0 rgba(0, 0, 0, 0.08);
}
.col-student .student-card.duo {
  background: var(--green-dark) !important;
}

.avatar-wrap {
  inline-size: 2em;
  block-size: 2em;
  border-radius: 0.75em;
  border: 0.125em solid var(--yellow);
  box-shadow: 0 0.625em 1.25em rgba(0, 0, 0, 0.18);
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.avatar-wrap .avatar {
  inline-size: 100%;
  block-size: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.col-student .meta {
  display: grid;
  min-width: 0;
}
.col-student .meta .name {
  font-weight: 900;
  font-size: 1.2em;
  line-height: 1.1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-align: left;
}
.col-student .meta .time {
  font-weight: 800;
  font-size: 0.82em;
  line-height: 1.1;
  color: var(--ink-50);
  text-align: left;
}

.col-carry {
  text-align: center;
}
.currency {
  font-weight: 900;
  font-size: 1.1em;
}

.th-prikaz {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1em;
}
.th-prikaz span {
  line-height: 1;
}
.th-prikaz .button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  block-size: var(--control-h);
  padding-inline: 1.1em;
  border: 0;
  border-radius: var(--radius);
  background: var(--yellow);
  font-weight: 800;
  font-size: 0.9em;
  cursor: pointer;
  box-shadow: 0 0.6em 0.9em rgba(0, 0, 0, 0.16),
    inset 0 -0.15em 0 rgba(0, 0, 0, 0.08);
  transition: transform 0.05s ease, filter 0.15s ease;
}
.th-prikaz .button:hover {
  filter: brightness(0.98);
}
.th-prikaz .button:active {
  transform: translateY(0.06em);
}

.col-prikaz {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1em;
}
.select-pill {
  position: relative;
  display: inline-flex;
  align-items: center;
  block-size: 4em;
  padding: 0 0.8em;
  border-radius: var(--radius);
  background: var(--green);
  box-shadow: inset 0 -0.15em 0 rgba(0, 0, 0, 0.08);
}

.select-pill.duo {
  background: var(--green-dark) !important;
}

.icon-square {
  inline-size: 3em;
  block-size: 2.5em;
  border-radius: 0.75em;
  border: 0;
  background: var(--yellow);
  display: grid;
  place-items: center;
  cursor: pointer;
  box-shadow: 0 0.6em 0.9em rgba(0, 0, 0, 0.16),
    inset 0 -0.15em 0 rgba(0, 0, 0, 0.08);
  transition: transform 0.05s ease, filter 0.15s ease,
    background-color 0.15s ease;
}
.icon-square:hover {
  background-color: #f4ab2a;
}
.icon-square:active {
  background-color: #f4ab2a;
}
.icon-square img {
  inline-size: 1.3em;
  block-size: 1.3em;
  object-fit: contain;
}

.icon-bell {
  background: #f86e5c;
  inline-size: 1.8em;
  block-size: 1.8em;
  margin: auto;
  border-radius: 0.6em;

  img {
    font-size: 0.7em;
  }
}

.col-paid {
  display: grid;
  place-items: center;
}
.pill.paid {
  inline-size: 3em;
  block-size: 2.7em;
  border-radius: 0.75em;
  background: var(--yellow);
  border: 0;
  cursor: pointer;
  display: grid;
  place-items: center;
  box-shadow: 0 0.6em 0.9em rgba(0, 0, 0, 0.16),
    inset 0 -0.15em 0 rgba(0, 0, 0, 0.08);
  transition: transform 0.05s ease, filter 0.15s ease, opacity 0.2s ease;
}
.pill.paid[disabled] {
  opacity: 0.6;
  cursor: not-allowed;
  pointer-events: none;
  background-color: #f4ab2a;
}
// .pill.paid.off {
//   opacity: 0.6;
// }
.pill.paid:hover {
  background-color: #f4ab2a;
}
.pill.paid:active {
  background-color: #f4ab2a;
}

.bar-wide {
  display: flex;
  align-items: center;
  justify-content: space-around;
  gap: 0.6em;
  padding: 0.5em 0.6em;
  border-radius: var(--radius);
  background: var(--green);
  font-weight: 900;
  font-size: 1.5em;
  box-shadow: inset 0 -0.15em 0 rgba(0, 0, 0, 0.08);
}

.bar-wide.duo {
  background: var(--green-dark) !important;
}
.bar-wide .num {
  min-width: 7ch;
  text-align: center;
}

.select-pill .native:focus-visible,
.th-prikaz .button:focus-visible,
.icon-square:focus-visible,
.pill.paid:focus-visible {
  outline: none;
  outline-offset: none;
}

@media (prefers-reduced-motion: reduce) {
  .th-prikaz .button,
  .icon-square,
  .pill.paid {
    transition: none;
  }
}

@media (max-width: 1200px) {
  .grid-platby {
    grid-template-columns:
      [idx] 2.5ch [student] minmax(4em, 0.5fr) [carry] 4em [fee] minmax(
        4em,
        0.4fr
      )
      [spacer] 5em [lessA] 6em [lessB] 6em [lessC] 6em [paid] 4em;
    column-gap: 0.5em;
  }

  .col-index {
    font-size: 1.4em;
  }

  .col-student .student-card {
    padding: 0.5em 0.7em;
    gap: 0.6em;
  }

  .col-student .meta .name {
    font-size: 1em;
  }
  .col-student .meta .time {
    font-size: 0.75em;
  }

  .currency {
    font-size: 1em;
  }

  .select-pill {
    block-size: 2.5em;
    padding: 0 0.6em;
  }

  .bar-wide {
    font-size: 1.1em;
    padding: 0.4em 0.5em;
    gap: 0.4em;
  }
}

@media screen and (max-width: 1023px) {
  .platby {
    font-size: 60%;
  }
}

@media screen and (max-width: 819px) {
  .platby {
    font-size: 55%;
  }
}

.empty-state {
  padding: 1.25em 1em;
  text-align: center;
  font-weight: 700;
  color: var(--ink-50);
  border-radius: var(--radius);
  background: #f3f3f3;
  box-shadow: inset 0 -0.15em 0 rgba(0, 0, 0, 0.06);
  margin-top: 3em;
}
.icon-square.disabled-mail {
  cursor: not-allowed !important;
  opacity: 0.7;
}
.icon-square.disabled-mail:hover {
  background-color: #f4ab2a !important;
}
</style>
