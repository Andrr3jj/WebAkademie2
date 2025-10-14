<template>
  <div v-if="showInvalidDialog" class="invalid-dialog">
    <div class="dialog-backdrop"></div>
    <div class="dialog">
      <p>
        Nasledujúce pesničky sú momentálne <strong>neaktívne</strong>:<br />
        <em>{{ invalidSongs.join(", ") }}</em>
      </p>
      <button class="button green" @click="onConfirmSkipInvalid">
        Pokračovať bez nich
      </button>
      <button class="button red" @click="onCancelGenerate">
        Zrušiť generovanie
      </button>
    </div>
  </div>

  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <h5>Darčekové poukážky / Kódy do knihy</h5>

      <div class="line horizontal w-80"></div>

      <p class="nadpis">Generovanie darčekových poukážok</p>
      <div class="generovanie">
        <div class="row-form vyber-switch">
          <div>
            <label style="margin-bottom: 0.3em">
              {{ vyberMode === "knihy" ? "Vyber knižky" : "Vyber zápisy" }}
            </label>
            <div
              class="arrow"
              :class="{ open: vyberMode === 'zapisy' }"
              @click="toggleVyberMode"
              style="
                display: inline-flex;
                align-items: center;
                cursor: pointer;
                margin-left: 1em;
              "
              title="Prepnúť výber"
            >
              <RefreshCcw style="width: 1.25em" />
            </div>
          </div>
          <multiselect
            v-if="vyberMode === 'knihy'"
            class="my-multiselect"
            v-model="generovanie.products"
            :options="availableProducts"
            :multiple="true"
            label="label"
            track-by="name"
            :placeholder="isOpen ? '' : 'Žiadne knihy'"
            @open="isOpen = true"
            @close="isOpen = false"
            :close-on-select="false"
          >
            <template #noResult>
              <span>Nenašiel sa žiadny zápis.</span>
            </template>
          </multiselect>

          <multiselect
            v-if="vyberMode === 'zapisy'"
            class="my-multiselect"
            v-model="generovanie.zapisy"
            :options="pagedZapisy"
            :multiple="true"
            label="label"
            track-by="name"
            :placeholder="isOpen ? '' : 'Žiadny zápis'"
            @open="isOpen = true"
            @close="isOpen = false"
            :close-on-select="false"
            :max-height="350"
          >
            <template #afterList>
              <div class="dropdown-pagination">
                <span
                  class="arrow-btn"
                  :class="{ disabled: zapisyPage === 0 }"
                  @click.stop="zapisyPage > 0 && zapisyPage--"
                  title="Predchádzajúce"
                  >&lt;</span
                >
                <span class="page-info">
                  {{ zapisyPage + 1 }} / {{ zapisyPageCount }}
                </span>
                <span
                  class="arrow-btn"
                  :class="{ disabled: zapisyPage >= zapisyPageCount - 1 }"
                  @click.stop="zapisyPage < zapisyPageCount - 1 && zapisyPage++"
                  title="Ďalšie"
                  >&gt;</span
                >
              </div>
            </template>
            <template #noResult>
              <span>Nenašiel sa žiadny zápis.</span>
            </template>
          </multiselect>
        </div>

        <div class="row-form">
          <label for="accesses">Vyber prístupy:</label>
          <select v-model="generovanie.accesses">
            <option disabled value="">Žiadny prístup</option>
            <option
              v-for="acc in availableAccesses"
              :key="acc.id"
              :value="acc.id"
            >
              {{ acc.label }}
            </option>
          </select>
        </div>

        <div class="row-form">
          <label>Vyber predplatné:</label>
          <select v-model="generovanie.subscription">
            <!-- len ak ešte nie je nastavené, ponúkni None -->
            <option v-if="!generovanie.subscription" disabled value="">
              Žiadne predplatné
            </option>
            <option
              v-for="sub in availableSubscriptions"
              :key="sub.id"
              :value="sub.id"
            >
              {{ sub.label }}
            </option>
          </select>
        </div>

        <div class="row-form">
          <label for="spec-kod">Špeciálny kód:</label>
          <input
            ref="spec-kod"
            type="text"
            name="spec-kod"
            placeholder="HELI-2024-XXXX"
            v-model="generovanie.specKod"
            maxlength="14"
            @input="onSpecKodInput"
          />
          <div v-if="specKodProb">
            <span style="font-size: 0.8em; color: #666">{{ specKodProb }}</span>
          </div>
        </div>

        <div class="row-form">
          <label for="value">Hodnota v €:</label>
          <input
            ref="value"
            type="number"
            min="0"
            step="1"
            name="value"
            placeholder="0"
            v-model="generovanie.value"
            class="hodnota"
          />
        </div>

        <div class="row-form">
          <label for="kusov">Počet ks:</label>
          <input
            ref="kusov"
            type="number"
            name="kusov"
            placeholder="0"
            v-model="pocetKS"
            class="hodnota"
          />
        </div>

        <div class="row-form">
          <label>Generovať kódy:</label>
          <div
            class="button"
            @click="generujPoukažky()"
            :class="{ disabled: generating }"
          >
            <p v-if="!generating">Vytvoriť</p>
            <p v-else>Generujem...</p>
          </div>
        </div>
      </div>

      <div v-if="generatedCodes.length" class="generated-codes">
        <h3 style="font-size: 1.2em; margin: 1em 0 0.2em 0">
          Vygenerované kódy:
        </h3>
        <ul style="list-style: none; padding: 0">
          <li v-for="(code, idx) in generatedCodes" :key="idx">
            {{ code }}
          </li>
        </ul>
        <button class="button" @click="downloadCSV" style="margin-top: 0.5em">
          Stiahnuť ako CSV
        </button>
      </div>

      <div class="all-statistic">
        <div class="darcekova-statistic">
          <p class="nadpis">Darčekové poukážky</p>
          <div class="box-statistic">
            <div class="stat-item">
              <p class="podnadpis">Zakúpené:</p>
              <p class="hodnota">{{ statsDarcekove.zakupene }}</p>
            </div>
            <div class="stat-item">
              <p class="podnadpis">Využité:</p>
              <p class="hodnota">{{ statsDarcekove.vyuzite }}</p>
            </div>
            <div class="stat-item">
              <p class="podnadpis">Nevyužité:</p>
              <p class="hodnota">{{ statsDarcekove.nevyuzite }}</p>
            </div>
          </div>
          <div class="button" @click="showTable('user')">
            <p>{{ showDarcekoveTable ? "Skryť" : "Zobraziť" }}</p>
          </div>
        </div>
        <div class="darcekova-statistic">
          <p class="nadpis">Kódy do knižky</p>
          <div class="box-statistic">
            <div class="stat-item">
              <p class="podnadpis">Vygenerované:</p>
              <p class="hodnota">{{ statsKody.vygenerovane }}</p>
            </div>
            <div class="stat-item">
              <p class="podnadpis">Využité:</p>
              <p class="hodnota">{{ statsKody.vyuzite }}</p>
            </div>
            <div class="stat-item">
              <p class="podnadpis">Nevyužité:</p>
              <p class="hodnota">{{ statsKody.nevyuzite }}</p>
            </div>
          </div>
          <div class="button" @click="showTable('admin')">
            <p>{{ showKodyTable ? "Skryť" : "Zobraziť" }}</p>
          </div>
        </div>
      </div>

      <!-- TABUĽKY -->
      <div ref="tablesWrapper">
        <!-- USER TABLE -->
        <div v-if="showDarcekoveTable" class="darceky-table">
          <div class="table-top">
            <p class="Bahnschrift">
              Email:
              <SortArrow
                column="origin"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="onSortChange"
              />
            </p>
            <p class="Bahnschrift">
              Kód poukážky:
              <SortArrow
                column="code"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="onSortChange"
              />
            </p>
            <p class="Bahnschrift">
              Zakúpené:
              <SortArrow
                column="timestamp"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="onSortChange"
              />
            </p>
            <p class="Bahnschrift">
              Využil email:
              <SortArrow
                column="used_by"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="onSortChange"
              />
            </p>
            <p class="Bahnschrift">
              Platnosť:
              <SortArrow
                column="valid_until"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="onSortChange"
              />
            </p>
            <p class="Bahnschrift">
              Hodnota
              <SortArrow
                column="value"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="onSortChange"
              />
            </p>
          </div>
          <div class="table">
            <div v-for="(p, idx) in zoradeneDarcekovePoukazky" :key="p.code">
              <div
                v-if="isEdit('user', idx)"
                class="table-row edit-mode"
                v-click-outside="{
                  handler: zrusEditaciu,
                  events: ['mousedown'],
                }"
                :ref="setEditRowRef"
              >
                <input
                  class="item"
                  v-model="editForm.email"
                  placeholder="Email"
                  @keyup.enter="ulozEditaciu(p)"
                />
                <input
                  class="item"
                  v-model="editForm.code"
                  placeholder="Kód"
                  disabled
                />
                <input
                  class="item"
                  type="date"
                  v-model="editForm.timestamp"
                  @keyup.enter="ulozEditaciu(p)"
                />
                <input
                  class="item"
                  v-model="editForm.used_by"
                  placeholder="Využil email"
                  disabled
                />
                <input
                  class="item"
                  type="date"
                  v-model="editForm.valid_until"
                />
                <input
                  class="item"
                  type="number"
                  v-model="editForm.value"
                  placeholder="Hodnota"
                />
                <div class="edit-actions">
                  <select v-model="editForm.accesses">
                    <option disabled value="">Žiadny prístup</option>
                    <option
                      v-for="acc in availableAccesses"
                      :key="acc.id"
                      :value="acc.id"
                    >
                      {{ acc.label }}
                    </option>
                  </select>
                  <div class="dropdown-wrapper">
                    <button
                      class="button dropdown-btn"
                      @click.stop="onOpenBooks"
                    >
                      Vybrať knihy
                      <span v-if="editForm.products.length"
                        >({{ editForm.products.length }})</span
                      >
                    </button>

                    <div
                      v-if="showBooksPicker"
                      class="picker-popover"
                      @click.stop
                    >
                      <h5>Vyber knihy</h5>
                      <div class="picker-list">
                        <label v-for="opt in availableProducts" :key="opt.name">
                          <input
                            type="checkbox"
                            :value="opt"
                            v-model="editForm.products"
                          />
                          {{ opt.label }}
                        </label>
                      </div>
                      <button
                        class="button green"
                        @click="showBooksPicker = false"
                      >
                        Hotovo
                      </button>
                    </div>
                  </div>

                  <div class="dropdown-wrapper">
                    <button
                      class="button dropdown-btn"
                      @click.stop="onOpenZapisy"
                    >
                      Vybrať zápisy
                      <span v-if="editForm.zapisy.length"
                        >({{ editForm.zapisy.length }})</span
                      >
                    </button>

                    <div
                      v-if="showZapisyPicker"
                      class="picker-popover"
                      @click.stop
                    >
                      <h5>Vyber zápisy</h5>
                      <div class="picker-list">
                        <label v-for="opt in availableZapisy" :key="opt.id">
                          <input
                            type="checkbox"
                            :value="opt"
                            v-model="editForm.zapisy"
                          />
                          {{ opt.label }}
                        </label>
                      </div>
                      <button
                        class="button green"
                        @click="showZapisyPicker = false"
                      >
                        Hotovo
                      </button>
                    </div>
                  </div>
                  <button @click.stop="ulozEditaciu(p)" class="button green">
                    Aplikovať
                  </button>
                  <button
                    @click.stop="confirmDelete(p.id)"
                    :class="[
                      'button',
                      confirmDeleteId === p.id ? 'red' : 'red',
                    ]"
                  >
                    {{ confirmDeleteId === p.id ? "Naozaj?" : "Vymazať" }}
                  </button>
                  <button @click.stop="zrusEditaciu()" class="button gray">
                    Zrušiť
                  </button>
                </div>
              </div>
              <div
                v-else
                class="table-row"
                @dblclick="zacniEditaciu('user', idx, p)"
              >
                <span class="item">{{ p.origin || "--" }}</span>
                <span class="item">{{ p.code || "--" }}</span>
                <span class="item">{{ p.timestamp || "--" }}</span>
                <span class="item">
                  {{
                    Array.isArray(p.used_by_emails)
                      ? p.used_by_emails.join(", ")
                      : p.used_by || "--"
                  }}
                </span>
                <span class="item">{{
                  formatDateInput(p.valid_until) || "--"
                }}</span>
                <span class="item">
                  <template v-if="!p.value_original && !p.value"> -- </template>
                  <template v-else>
                    {{ Number(p.value) || 0 }} /
                    {{ Number(p.value_original) || 0 }} €
                  </template>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- ADMIN TABLE -->
        <div v-if="showKodyTable" class="darceky-table" style="margin-top: 1em">
          <div class="table-top">
            <p class="Bahnschrift">
              Email:
              <SortArrow
                column="origin"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="onSortChange"
              />
            </p>
            <p class="Bahnschrift">
              Kód poukážky:
              <SortArrow
                column="code"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="onSortChange"
              />
            </p>
            <p class="Bahnschrift">
              Zakúpené:
              <SortArrow
                column="timestamp"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="onSortChange"
              />
            </p>
            <p class="Bahnschrift">
              Využil email:
              <SortArrow
                column="used_by"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="onSortChange"
              />
            </p>
            <p class="Bahnschrift">
              Platnosť:
              <SortArrow
                column="valid_until"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="onSortChange"
              />
            </p>
            <p class="Bahnschrift">
              Hodnota
              <SortArrow
                column="value"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="onSortChange"
              />
            </p>
          </div>
          <div class="table">
            <div v-for="(p, idx) in zoradeneKodyDoKnihy" :key="p.code">
              <div
                v-if="isEdit('admin', idx)"
                class="table-row edit-mode"
                v-click-outside="{
                  handler: zrusEditaciu,
                  events: ['mousedown'],
                }"
                :ref="setEditRowRef"
              >
                <input
                  class="item"
                  v-model="editForm.email"
                  placeholder="Email"
                  @keyup.enter="ulozEditaciu(p)"
                />
                <input
                  class="item"
                  v-model="editForm.code"
                  placeholder="Kód"
                  @keyup.enter="ulozEditaciu(p)"
                />
                <input
                  class="item"
                  type="date"
                  v-model="editForm.timestamp"
                  @keyup.enter="ulozEditaciu(p)"
                  disabled
                />
                <input
                  class="item"
                  v-model="editForm.used_by"
                  placeholder="Využil email"
                  disabled
                />
                <input
                  class="item"
                  type="date"
                  v-model="editForm.valid_until"
                />
                <input
                  class="item"
                  type="number"
                  v-model="editForm.value"
                  placeholder="Hodnota"
                />
                <div class="edit-actions">
                  <select v-model="editForm.accesses">
                    <option disabled value="">Žiadny prístup</option>
                    <option
                      v-for="acc in availableAccesses"
                      :key="acc.id"
                      :value="acc.id"
                    >
                      {{ acc.label }}
                    </option>
                  </select>
                  <div class="dropdown-wrapper">
                    <button
                      class="button dropdown-btn"
                      @click.stop="onOpenBooks"
                    >
                      Vybrať knihy
                      <span v-if="editForm.products.length"
                        >({{ editForm.products.length }})</span
                      >
                    </button>

                    <div
                      v-if="showBooksPicker"
                      class="picker-popover"
                      @click.stop
                    >
                      <h5>Vyber knihy</h5>
                      <div class="picker-list">
                        <label v-for="opt in availableProducts" :key="opt.name">
                          <input
                            type="checkbox"
                            :value="opt"
                            v-model="editForm.products"
                          />
                          {{ opt.label }}
                        </label>
                      </div>
                      <button
                        class="button green"
                        @click="showBooksPicker = false"
                      >
                        Hotovo
                      </button>
                    </div>
                  </div>

                  <div class="dropdown-wrapper">
                    <button
                      class="button dropdown-btn"
                      @click.stop="onOpenZapisy"
                    >
                      Vybrať zápisy
                      <span v-if="editForm.zapisy.length"
                        >({{ editForm.zapisy.length }})</span
                      >
                    </button>

                    <div
                      v-if="showZapisyPicker"
                      class="picker-popover"
                      @click.stop
                    >
                      <h5>Vyber zápisy</h5>
                      <div class="picker-list">
                        <label v-for="opt in availableZapisy" :key="opt.id">
                          <input
                            type="checkbox"
                            :value="opt"
                            v-model="editForm.zapisy"
                          />
                          {{ opt.label }}
                        </label>
                      </div>
                      <button
                        class="button green"
                        @click="showZapisyPicker = false"
                      >
                        Hotovo
                      </button>
                    </div>
                  </div>

                  <button @click.stop="ulozEditaciu(p)" class="button green">
                    Aplikovať
                  </button>
                  <button
                    @click.stop="confirmDelete(p.id)"
                    :class="[
                      'button',
                      confirmDeleteId === p.id ? 'red' : 'red',
                    ]"
                  >
                    {{ confirmDeleteId === p.id ? "Naozaj?" : "Vymazať" }}
                  </button>
                  <button @click.stop="zrusEditaciu()" class="button yellow">
                    Zrušiť
                  </button>
                </div>
              </div>
              <div
                v-else
                class="table-row"
                @dblclick="zacniEditaciu('admin', idx, p)"
              >
                <span class="item">{{ p.origin || "--" }}</span>
                <span class="item">{{ p.code || "--" }}</span>
                <span class="item">{{ p.timestamp || "--" }}</span>
                <span class="item">
                  {{
                    Array.isArray(p.used_by_emails)
                      ? p.used_by_emails.join(", ")
                      : p.used_by || "--"
                  }}
                </span>
                <span class="item">{{
                  formatDateInput(p.valid_until) || "--"
                }}</span>
                <span class="item">
                  <template v-if="!p.value_original && !p.value"> -- </template>
                  <template v-else>
                    {{ Number(p.value) || 0 }} /
                    {{ Number(p.value_original) || 0 }} €
                  </template>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showLoadMore && shouldShowLoadMore" class="table-actions">
        <button class="button yellow" @click="limit += 20">
          Načítať ďalšie
        </button>
      </div>
    </div>
  </section>
</template>

<script>
import Multiselect from "vue-multiselect";
import { RefreshCcw } from "lucide-vue-next";
import { useClickOutside } from "@vueuse/core";
import SortArrow from "@/components/Functionality/SortArrow.vue";

export default {
  components: { Multiselect, RefreshCcw, SortArrow },
  data() {
    return {
      API: "https://heligonkovaakademia.sk/api/product/gift_card/",
      poukazky: [],
      loading: false,
      page: 1,
      limit: 20,
      loadingMore: false,
      generating: false,
      isOpen: false,
      allCodes: [],
      generatedCodes: [],
      allPoukažkyDetails: [],
      availableZapisy: [],
      vyberMode: "knihy",
      zapisyPerPage: 10,
      zapisyPage: 0,
      stats: {
        zakupene: 0,
        vyuzite: 0,
        nevyuzite: 0,
        kniha: 0,
        knihaVyuzite: 0,
        knihaNevyuzite: 0,
      },
      availableProducts: [],
      availableAccesses: [
        { id: "data_knizky_prvy_diel", label: "Prvý diel" },
        { id: "data_knizky_druhy_diel", label: "Druhý diel" },
        { id: "data_knizky_treti_diel", label: "Tretí diel" },
        { id: "data_knizky_stvrty_diel", label: "Štvrtý diel" },
        { id: "data_knizky_piaty_diel", label: "Piaty diel" },
        { id: "data_knizky_siesty_diel", label: "Šiesty diel" },
        { id: "data_knizky_siedmy_diel", label: "Siedmy diel" },
        { id: "data_knizky_osmy_diel", label: "Ôsmy diel" },
        { id: "data_knizky_deviaty_diel", label: "Deviaty diel" },
        { id: "data_knizky_desiaty_diel", label: "Desiaty diel" },
      ],
      availableSubscriptions: [
        { id: "3", label: "Ročné predplatné" },
        { id: "2", label: "Polročné predplatné" },
        { id: "1", label: "Mesačné predplatné" },
      ],
      generovanie: {
        knizka: "1",
        products: [],
        specKod: "",
        pocetKS: 0,
        value: 0,
        value_original: 0,
        accesses: "",
        valid_until: "",
        subscription: "",
        zapisy: [],
      },
      editovanyRiadok: null,
      editForm: {
        email: "",
        code: "",
        used_by: "",
        value: 0,
        value_original: 0,
        timestamp: "",
        valid_until: "",
        giftable_products: [],
        giftable_accesses: [],
      },
      showKodyTable: false,
      showDarcekoveTable: true,
      specKodProb: "",
      editRowRef: null,
      stopRowClickOutside: null,
      stopClickOutside: null,
      stopEscListener: null,
      confirmDeleteId: null,
      escListener: null,
      showBooksPicker: false,
      showZapisyPicker: false,
      showInvalidDialog: false,
      invalidSongs: [],
      pendingProductIds: [],
      sortKey: "",
      sortDirection: "",
    };
  },
  computed: {
    vsetkyDarcekove() {
      return this.zoradenePoukazky.filter(
        (p) => Number(p.value_original || 0) > 0
      );
    },
    vsetkyKody() {
      return this.zoradenePoukazky.filter(
        (p) => Number(p.value_original || 0) === 0
      );
    },
    shouldShowLoadMore() {
      if (this.showDarcekoveTable) {
        return (
          this.zoradeneDarcekovePoukazky.length < this.vsetkyDarcekove.length
        );
      }
      if (this.showKodyTable) {
        return this.zoradeneKodyDoKnihy.length < this.vsetkyKody.length;
      }
      return false;
    },
    vybraneZapisy() {
      return this.availableZapisy.filter((z) =>
        this.editForm.giftable_products.includes(z.id)
      );
    },
    pagedZapisy() {
      const start = this.zapisyPage * this.zapisyPerPage;
      return this.availableZapisy.slice(start, start + this.zapisyPerPage);
    },
    zapisyPageCount() {
      return Math.ceil(this.availableZapisy.length / this.zapisyPerPage);
    },
    darcekovePoukazky() {
      // Ak máš value_original v detaile, používaj ju, inak len value
      return this.poukazky.filter((p) => Number(p.value_original || 0) > 0);
    },
    // Kódy do knižky: všetky, ktoré NIKDY nemali hodnotu > 0
    kodyDoKnihy() {
      return this.poukazky.filter((p) => Number(p.value_original || 0) === 0);
    },
    statsDarcekove() {
      const arr = this.allPoukažkyDetails.filter(
        (p) => Number(p.value_original || 0) > 0
      );
      return {
        zakupene: arr.length,
        vyuzite: arr.filter((p) => !!p.used_by).length,
        nevyuzite: arr.filter((p) => !p.used_by).length,
      };
    },
    statsKody() {
      const arr = this.allPoukažkyDetails.filter(
        (p) => Number(p.value_original || 0) === 0
      );
      return {
        vygenerovane: arr.length,
        vyuzite: arr.filter((p) => !!p.used_by).length,
        nevyuzite: arr.filter((p) => !p.used_by).length,
      };
    },
    showLoadMore() {
      // nechceme tlačidlo, ak je otvorená user tabuľka a je prázdna
      if (this.showDarcekoveTable) {
        return (
          this.darcekovePoukazky.length > 0 &&
          this.poukazky.length < this.allCodes.length
        );
      }
      // nechceme tlačidlo, ak je otvorená admin tabuľka a je prázdna
      if (this.showKodyTable) {
        return (
          this.kodyDoKnihy.length > 0 &&
          this.poukazky.length < this.allCodes.length
        );
      }
      return false;
    },
    zoradenePoukazky() {
      let arr = [...this.allPoukažkyDetails];
      const { sortKey, sortDirection } = this;
      if (!sortKey || !sortDirection) return arr;

      return arr.sort((a, b) => {
        let aVal = a[sortKey];
        let bVal = b[sortKey];

        // Čísla
        if (sortKey === "value" || sortKey === "value_original") {
          aVal = Number(aVal) || 0;
          bVal = Number(bVal) || 0;
          return sortDirection === "asc" ? aVal - bVal : bVal - aVal;
        }

        // Dátumy
        if (sortKey === "timestamp" || sortKey === "valid_until") {
          aVal = Number(aVal) || Date.parse(aVal) || 0;
          bVal = Number(bVal) || Date.parse(bVal) || 0;
          return sortDirection === "asc" ? aVal - bVal : bVal - aVal;
        }

        // Stringy (email, code...)
        aVal = (aVal || "").toString();
        bVal = (bVal || "").toString();
        return sortDirection === "asc"
          ? aVal.localeCompare(bVal)
          : bVal.localeCompare(aVal);
      });
    },
    zoradeneDarcekovePoukazky() {
      // zorad zoradenePoukazky a vyfiltruj len darčekové poukážky
      const zoradene = this.zoradenePoukazky.filter(
        (p) => Number(p.value_original || 0) > 0
      );
      return zoradene.slice(0, this.limit);
    },
    zoradeneKodyDoKnihy() {
      const zoradene = this.zoradenePoukazky.filter(
        (p) => Number(p.value_original || 0) === 0
      );
      return zoradene.slice(0, this.limit);
    },
  },
  methods: {
    onSortChange({ key, direction }) {
      this.sortKey = key;
      this.sortDirection = direction;
    },
    async loadNoteIdsForFolder(name) {
      try {
        const res = await fetch(
          `https://heligonkovaakademia.sk/api/folder/list.php?name=${encodeURIComponent(
            name
          )}`
        );
        const json = await res.json();
        return Array.isArray(json.data) ? json.data : [];
      } catch {
        return [];
      }
    },

    // Načíta všetky noty
    async loadAllNotes() {
      try {
        const res = await fetch(
          "https://heligonkovaakademia.sk/api/noty/list.php"
        );
        const json = await res.json();
        return Array.isArray(json)
          ? json
          : Array.isArray(json.data)
          ? json.data
          : [];
      } catch {
        return [];
      }
    },

    async loadAllNotesWithProductId() {
      const res = await fetch(
        "https://heligonkovaakademia.sk/api/noty/list.php"
      );
      const json = await res.json();
      const raw = Array.isArray(json.data) ? json.data : [];

      return raw.map((z) => ({
        id: String(z.id),
        name: z.nazov, // tu použijeme priamo "nazov"
        product_id: String(z.product_id || "0"),
        active: Boolean(z.stav_aktivne),
      }));
    },

    // Získa všetky product_id zo zvolených priečinkov
    async getProductIdsFromFolders(folders) {
      const noteIdSet = new Set();
      await Promise.all(
        folders.map(async (folder) => {
          const ids = await this.loadNoteIdsForFolder(folder.name);
          ids.forEach((id) => noteIdSet.add(String(id)));
        })
      );
      const allNotes = await this.loadAllNotes();
      return Array.from(noteIdSet)
        .map((id) => {
          const note = allNotes.find((n) => String(n.id) === id);
          return note?.product_id;
        })
        .filter((pid) => pid && /^\d+$/.test(pid));
    },
    async nacitajPriecinky() {
      try {
        const res = await fetch(
          "https://heligonkovaakademia.sk/api/folder/list.php"
        );
        const data = await res.json();
        this.availableProducts = data.data.map((name) => ({
          name, // tu sa bude volať folder/list.php?name=…
          label: name,
        }));
      } catch (e) {
        this.availableProducts = [];
      }
    },
    toggleVyberMode() {
      if (this.vyberMode === "knihy") {
        this.generovanie.zapisy = [...this.generovanie.products];
        this.vyberMode = "zapisy";
      } else {
        this.generovanie.products = [...this.generovanie.zapisy];
        this.vyberMode = "knihy";
      }
    },
    extractUsedByIds(val) {
      if (!val) return [];

      // multi-segment verzia
      if (val.includes("|")) {
        return val
          .split("|")
          .filter((s) => s.trim())
          .map((segment) => {
            // 1) Najskôr skúsiť formát '-> id'
            const arrowMatch = segment.match(/->\s*(\d+)/);
            if (arrowMatch) return arrowMatch[1];

            // 2) Formát 'uid:xxxx'
            const uidMatch = segment.match(/uid:(\d+)/);
            if (uidMatch) return uidMatch[1];

            // 3) Ak segment vyzerá ako celé číslo na konci
            const numMatch = segment.match(/(\d+)$/);
            if (numMatch) return numMatch[1];

            return null;
          })
          .filter(Boolean);
      }

      // Ak je to celé číslo (single použitie)
      if (/^\d+$/.test(val)) {
        return [val];
      }
      // uid:xxxx
      const uidMatch = val.match(/uid:(\d+)/);
      if (uidMatch) return [uidMatch[1]];

      // -> id na konci
      const arrowMatch = val.match(/->\s*(\d+)/);
      if (arrowMatch) return [arrowMatch[1]];

      // fallback: ak končí číslom
      const m = val.match(/(\d+)$/);
      return m ? [m[1]] : [];
    },

    async fetchAndAttachEmail(p) {
      const ids = [...new Set(this.extractUsedByIds(p.used_by))];
      p.used_by_emails = [];

      await Promise.all(
        ids.map(async (id) => {
          try {
            const res = await fetch(
              `https://heligonkovaakademia.sk/api/user/info/getAllInformationAboutUser.php/?id=${id}`
            );
            const json = await res.json();
            if (json?.data?.email) {
              p.used_by_emails.push(json.data.email);
            }
          } catch (e) {
            console.warn("Error fetching email for ID", id, e);
          }
        })
      );
    },
    async afterBatch() {
      await Promise.all(this.poukazky.map((p) => this.fetchAndAttachEmail(p)));
    },
    showTable(name) {
      if (name === "user") {
        this.showDarcekoveTable = true;
        this.showKodyTable = false;
      } else {
        this.showDarcekoveTable = false;
        this.showKodyTable = true;
      }
    },
    setEditRowRef(el) {
      this.editRowRef = el;
    },
    async nacitajAllStats() {
      try {
        const res = await fetch(this.API + "list.php");
        const json = await res.json();
        if (!json?.data || !Array.isArray(json.data))
          throw new Error("Invalid");

        // ⬇️ rovno to priraď do detailov
        this.allPoukažkyDetails = json.data;

        // a vypočítaj štatistiky
        this.vypocitajStats();
      } catch (e) {
        console.error("Chyba pri nacitani list.php:", e);
        this.allPoukažkyDetails = [];
      }
    },
    vypocitajStats() {
      let all = this.allPoukažkyDetails;
      this.stats.zakupene = all.filter((p) => Number(p.value) > 0).length;
      this.stats.vyuzite = all.filter((p) => p.used_by).length;
      this.stats.nevyuzite = all.filter((p) => !p.used_by).length;
      this.stats.kniha = all.length;
      this.stats.knihaVyuzite = all.filter((p) => p.used_by).length;
      this.stats.knihaNevyuzite = all.filter((p) => !p.used_by).length;
    },
    async nacitajBatch(page = 1) {
      this.loading = page === 1;
      this.loadingMore = page > 1;
      if (page === 1 || this.allCodes.length === 0) {
        let res = await fetch(this.API + "list.php");
        let data = await res.json();
        this.allCodes = data.data
          .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp))
          .map((p) => p.code);
      }
      let codesForPage = this.allCodes.slice(
        (page - 1) * this.limit,
        page * this.limit
      );
      if (!codesForPage.length) {
        this.loading = this.loadingMore = false;
        return;
      }
      let batch = await Promise.all(
        codesForPage.map((code) =>
          fetch(this.API + "view.php?code=" + encodeURIComponent(code))
            .then((r) => r.json())
            .then((res) => res.data[0])
        )
      );
      batch = batch.filter(
        (p) => !p.deleted || p.deleted === "false" || p.deleted === 0
      );
      await Promise.all(batch.map((p) => this.fetchAndAttachEmail(p)));
      this.poukazky = page === 1 ? batch : [...this.poukazky, ...batch];
      this.page = page;
      this.loading = this.loadingMore = false;
      this.vypocitajStats();
    },
    async nacitajZapisy() {
      try {
        const res = await fetch(
          "https://heligonkovaakademia.sk/api/product/stats/listZapis.php"
        );
        const json = await res.json();

        if (json.status !== "Request succesfull" || !Array.isArray(json.data)) {
          console.warn("Neočekávaný formát listZapis:", json);
          this.availableZapisy = [];
          return;
        }

        this.availableZapisy = json.data.map((z) => ({
          id: z.id,
          label: z.name,
          details: z.details,
          difficulty: z.difficulty,
        }));
      } catch (e) {
        console.error("Chyba pri načítaní zápisov:", e);
        this.availableZapisy = [];
      }
    },
    isEdit(typ, idx) {
      return (
        this.editovanyRiadok?.typ === typ && this.editovanyRiadok.idx === idx
      );
    },
    zacniEditaciu(typ, idx, p) {
      // 1) odstrániť staré click‐outside
      if (this.stopRowClickOutside) {
        this.stopRowClickOutside();
        this.stopRowClickOutside = null;
      }

      // 2) štart edit módu
      this.editovanyRiadok = { typ, idx };

      // 3) naparsovať pôvodné giftable_products
      let productIds = [];
      try {
        productIds = JSON.parse(p.giftable_products) || [];
      } catch (e) {
        console.warn("Chyba pri parse giftable_products:", e);
      }

      // 3.a) vybrať a vyňať predplatné z poľa
      const subIds = this.availableSubscriptions.map((s) => s.id);
      let subscription = "";
      const filtered = productIds.filter((pid) => {
        if (subIds.includes(String(pid))) {
          subscription = String(pid);
          return false;
        }
        return true;
      });

      // 4) vybrať priečinky (folders) podľa folder.name
      const selProducts = filtered
        .filter((pid) =>
          this.availableProducts.some((x) => String(x.name) === String(pid))
        )
        .map((pid) =>
          this.availableProducts.find((x) => String(x.name) === String(pid))
        );

      // 5) vybrať zápisy (noty) podľa ich id
      const selZapisy = filtered
        .filter((pid) =>
          this.availableZapisy.some((z) => String(z.id) === String(pid))
        )
        .map((pid) =>
          this.availableZapisy.find((z) => String(z.id) === String(pid))
        );

      // 6) parse giftable_accesses
      let accessIds = [];
      try {
        accessIds = JSON.parse(p.giftable_accesses) || [];
      } catch (e) {
        console.warn("Chyba pri parse giftable_accesses:", e);
      }
      const selAccess = accessIds[0] || "";

      // 7) naplniť editForm (v UI už NEZOBRAZUJ select pre subscription)
      this.editForm = {
        email: p.origin,
        code: p.code,
        value_original: Number(p.value_original || p.value || 0),
        value: Number(p.value || 0),
        timestamp: p.timestamp,
        valid_until: this.formatDateInput(p.valid_until),
        products: selProducts,
        zapisy: selZapisy,
        accesses: selAccess,
        used_by: p.used_by_emails?.join(", ") || p.used_by,
        subscription, // uložené, ale v UI skryté
      };

      // 8) znovu click‐outside pre zrušenie edit
      this.$nextTick(() => {
        this.stopRowClickOutside = useClickOutside(
          () => this.editRowRef,
          () => this.zrusEditaciu(),
          { eventName: "mousedown" }
        );
      });
    },

    async ulozEditaciu(p) {
      const formData = new FormData();
      formData.append("id", p.id);
      formData.append("code", this.editForm.code);

      // pôvodná hodnota ostáva nezmenená
      formData.append(
        "value_original",
        String(p.value_original ?? p.value ?? 0)
      );
      // nová upravená hodnota
      formData.append("value", String(this.editForm.value ?? 0));

      // platnosť
      const validRaw =
        this.editForm.valid_until === "neobmedzená"
          ? p.valid_until
          : this.toTimestamp(this.editForm.valid_until);
      formData.append("valid_until", validRaw);

      // priečinky (knihy)
      const folderIds = await this.getProductIdsFromFolders(
        this.editForm.products || []
      );
      folderIds.forEach((pid) => formData.append("giftable_products[]", pid));

      // zápisy
      (this.editForm.zapisy || []).forEach((z) =>
        formData.append("giftable_products[]", z.id)
      );

      // prístupy
      if (this.editForm.accesses) {
        const accId = this.editForm.accesses.id || this.editForm.accesses;
        formData.append("giftable_accesses[]", accId);
      }

      //  ⮕ subscription: nech sa vždy pripojí, hoci v UI ho netočíš
      if (this.editForm.subscription) {
        formData.append("giftable_products[]", this.editForm.subscription);
      }

      try {
        const resp = await fetch(this.API + "edit.php", {
          method: "POST",
          body: formData,
        });
        const text = await resp.text();
        const json = JSON.parse(text);

        if (json.status === "Request fullfiled") {
          this.$store.state.SnackBarText = "Údaje boli upravené";
          // obnov túto položku
          const singleRes = await fetch(
            this.API + "view.php?code=" + encodeURIComponent(p.code)
          );
          const singleJson = await singleRes.json();
          const updated = singleJson.data[0];
          const idx = this.poukazky.findIndex((x) => x.id === p.id);
          if (idx !== -1) this.poukazky.splice(idx, 1, updated);

          // prípadne ešte celé batch načítanie:
          await this.nacitajBatch(this.page);
        } else {
          this.$store.state.SnackBarText =
            "Úprava sa nepodarila: " + (json.data || json.status);
        }
      } catch (e) {
        this.$store.state.SnackBarText = "Chyba pri uložení: " + e.message;
      }

      this.zrusEditaciu();
    },
    handleEscKey(e) {
      if (e.key === "Escape" && this.editovanyRiadok) {
        this.zrusEditaciu();
      }
    },
    onOpenBooks() {
      this.showBooksPicker = !this.showBooksPicker;
      // zatvoríme zápisy ak sú otvorené
      if (this.showBooksPicker) this.showZapisyPicker = false;
    },
    // po kliknutí na “Vybrať zápisy”
    onOpenZapisy() {
      this.showZapisyPicker = !this.showZapisyPicker;
      if (this.showZapisyPicker) this.showBooksPicker = false;
    },
    zrusEditaciu() {
      if (this.stopRowClickOutside) {
        this.stopRowClickOutside();
        this.stopRowClickOutside = null;
      }

      this.showBooksPicker = false;
      this.showZapisyPicker = false;

      this.editovanyRiadok = null;
      this.editForm = {};
      this.editRowRef = null;
    },
    onSpecKodInput(e) {
      // 1) odstránime všetko okrem písmen a číslic
      let clean = e.target.value.replace(/[^A-Za-z0-9]/g, "").toUpperCase();
      // 2) obmedzíme až na 12 znakov
      clean = clean.slice(0, 12);
      // 3) rozdelíme na bloky po max. 4 znaky
      const parts = clean.match(/.{1,4}/g) || [];
      // 4) spojíme ich pomlčkami
      const formatted = parts.join("-");
      // 5) nastavíme do v-model a prepocítame pravdepodobnosť
      this.generovanie.specKod = formatted;
      this.specKodProb = this.pravdUhadnutia(formatted);
    },
    formatDateInput(ts) {
      if (!ts) return "";
      // ak je už string vo formáte YYYY-MM-DD
      if (typeof ts === "string" && ts.includes("-")) {
        const year = parseInt(ts.split("-")[0], 10);
        return year > 2200 ? "neobmedzená" : ts;
      }
      // inak je ts timestamp v sekundách
      const date = new Date(Number(ts) * 1000);
      const year = date.getUTCFullYear();
      if (year > 2200) return "neobmedzená";
      return date.toISOString().split("T")[0];
    },

    toTimestamp(dateStr) {
      return dateStr ? Math.floor(new Date(dateStr).getTime() / 1000) : "";
    },
    formatCode(code) {
      const clean = code.replace(/-/g, "");
      const groups = clean.match(/.{1,4}/g) || [];
      return groups.join("-");
    },
    async generujPoukažky() {
      this.generatedCodes = [];
      this.generating = true;

      if (!this.pocetKS || this.pocetKS < 1) {
        alert("Zadaj počet kusov!");
        this.generating = false;
        return;
      }

      // 1) poskladáme si všetky "song" objekty
      const noteIdSet = new Set();
      for (const folder of this.generovanie.products) {
        const ids = await this.loadNoteIdsForFolder(folder.name);
        ids.forEach((id) => noteIdSet.add(String(id)));
      }
      const allNotes = await this.loadAllNotesWithProductId();
      const songList = Array.from(noteIdSet).map(
        (id) =>
          allNotes.find((n) => n.id === id) || {
            id,
            name: `#${id}`,
            product_id: "0",
          }
      );

      // 2) nájdeme neaktívne
      const invalid = songList.filter((s) => s.product_id === "0");
      if (invalid.length) {
        // uložím si mená a zároveň platné IDčka
        this.invalidSongs = invalid.map((s) => s.name);
        this.pendingProductIds = songList
          .filter((s) => s.product_id !== "0")
          .map((s) => s.product_id);

        this.showInvalidDialog = true; // otvorí sa dialóg
        this.generating = false;
        return;
      }

      // 3) ak nič neaktívne, rovno sa generuje
      const productIds = songList.map((s) => s.product_id);
      this._doGenerate(productIds);
    },

    async _doGenerate(productIds) {
      // productIds: pole platných product_id, získané predvolávou
      for (let i = 0; i < Number(this.pocetKS); i++) {
        const formData = new FormData();
        // špeciálny kód
        formData.append("code", this.generovanie.specKod);
        // pôvodná aj zostávajúca hodnota
        formData.append("value", this.generovanie.value);
        formData.append("value_original", this.generovanie.value);
        // všetky platné product_id
        productIds.forEach((pid) =>
          formData.append("giftable_products[]", pid)
        );
        // prípadné predplatné
        if (this.generovanie.subscription) {
          formData.append("giftable_products[]", this.generovanie.subscription);
        }
        // prípadné prístupy
        if (this.generovanie.accesses) {
          formData.append("giftable_accesses[]", this.generovanie.accesses);
        }
        // valid_until – buď z datumu, alebo 200 rokov dopredu
        formData.append(
          "valid_until",
          this.generovanie.valid_until
            ? this.toTimestamp(this.generovanie.valid_until)
            : Math.floor(Date.now() / 1000) + 200 * 365 * 24 * 3600
        );

        try {
          const res = await fetch(this.API + "create.php", {
            method: "POST",
            body: formData,
          });
          const json = await res.json();
          // formátovanie kódu do pomlčkového tvaru
          this.generatedCodes.push(this.formatCode(json.data || "CHYBA"));
        } catch {
          this.generatedCodes.push("CHYBA");
        }
      }

      // po vygenerovaní obnovíme štatistiky a prvú stranu záznamov
      await this.nacitajAllStats();
      await this.nacitajBatch(1);
      this.generating = false;
    },
    onConfirmSkipInvalid() {
      this.showInvalidDialog = false;
      // zavolám generovanie už s tými platnými IDčkami
      this._doGenerate(this.pendingProductIds);
      this.pendingProductIds = [];
    },
    onCancelGenerate() {
      this.showInvalidDialog = false;
      this.generating = false;
      this.pendingProductIds = [];
    },
    downloadCSV() {
      const blob = new Blob([this.generatedCodes.join("\n")], {
        type: "text/csv",
      });
      const link = document.createElement("a");
      link.href = URL.createObjectURL(blob);
      link.download = "poukazky.csv";
      link.click();
    },
    daysLeft(valid_until) {
      return Math.max(
        0,
        Math.floor((Number(valid_until) - Date.now() / 1000) / 86400)
      );
    },
    formatDate(ts) {
      if (!ts) return "";
      const d = new Date(ts.replace(" ", "T"));
      return `${d.getDate()}.${d.getMonth() + 1}.${d.getFullYear()}`;
    },
    pravdUhadnutia(prefix) {
      if (!prefix) return "";
      const cleaned = prefix.replace(/-/g, "");
      const rem = 12 - cleaned.length;
      if (rem <= 0) return "";
      return `Pravd. uhádnutia: 1 z ${Math.pow(23, rem)}`;
    },
    async confirmDelete(id) {
      if (this.confirmDeleteId === id) {
        await this.vymazat(id);
        this.confirmDeleteId = null;
      } else {
        this.confirmDeleteId = id;
        setTimeout(() => {
          if (this.confirmDeleteId === id) this.confirmDeleteId = null;
        }, 5000);
      }
    },
    async vymazat(id) {
      if (!/^\d+$/.test(String(id))) {
        this.$store.state.SnackBarText = "Neplatné ID";
        return;
      }

      try {
        const resp = await fetch(
          `https://heligonkovaakademia.sk/api/product/gift_card/delete.php?id=${id}`
        );
        const text = await resp.text();

        let data;
        try {
          data = JSON.parse(text);
        } catch (e) {
          this.$store.state.SnackBarText = `Server neposlal platný JSON: ${
            e.message
          }\nOdpoveď: ${text || "(prázdne)"}`;
          return;
        }

        if (data?.status === "Request fullfiled") {
          this.$store.state.SnackBarText = "Položka bola úspešne vymazaná.";
          await this.nacitajBatch(this.page);
        } else {
          this.$store.state.SnackBarText =
            "Vymazanie sa nepodarilo: " + (data?.data || "Neznáma chyba");
        }
      } catch (e) {
        this.$store.state.SnackBarText = `Chyba siete pri mazaní: ${e.message}`;
      }
    },
  },
  mounted() {
    this.$nextTick(() => {
      const wrap = this.$el.querySelector(".multiselect__tags-wrap");
      if (wrap) {
        wrap.addEventListener("click", () => (this.isOpen = true));
      }
    });

    // 🆕 Načítaj všetky zápisy
    fetch("https://heligonkovaakademia.sk/api/noty/list.php")
      .then((res) => res.json())
      .then((data) => {
        if (Array.isArray(data)) {
          this.availableZapisy = data;
        } else {
          console.warn("Nesprávny formát zápisov:", data);
          this.availableZapisy = [];
        }
      })
      .catch((err) => {
        console.error("Chyba pri načítaní zápisov:", err);
        this.availableZapisy = [];
      });

    // ✅ Pôvodné volania
    this.nacitajPriecinky();
    this.nacitajBatch(1);
    this.nacitajAllStats().then(async () => {
      // 🔽 Tu doplníme fetchovanie emailov
      for (const p of this.allPoukažkyDetails) {
        await this.fetchAndAttachEmail(p);
      }
    });

    this.nacitajZapisy();
    this.specKodProb = this.pravdUhadnutia(this.generovanie.specKod);
    this.afterBatch();

    this.escListener = (e) => this.handleEscKey(e);
    window.addEventListener("keydown", this.escListener);
  },
  beforeUnmount() {
    window.removeEventListener("keydown", this.escListener);
  },
  watch: {
    "editForm.products": {
      handler: async function (newProducts) {
        const zapisySet = new Set();

        for (const product of newProducts) {
          try {
            const res = await fetch(
              `https://heligonkovaakademia.sk/api/folder/list.php?name=${encodeURIComponent(
                product.id
              )}`
            );
            const json = await res.json();
            const ids = Array.isArray(json.data) ? json.data : [];
            ids.forEach((id) => zapisySet.add(String(id)));
          } catch (e) {
            console.warn("Chyba pri načítaní priečinka:", product.id, e);
          }
        }

        const newZapisy = Array.from(zapisySet)
          .map((id) =>
            this.availableZapisy.find((z) => String(z.id) === String(id))
          )
          .filter(Boolean);

        // Nechceme duplikáty – spojíme s už existujúcimi, ale bez duplicit
        const allZapisy = [
          ...new Map(
            [...(this.editForm.zapisy || []), ...newZapisy].map((z) => [
              String(z.id),
              z,
            ])
          ).values(),
        ];

        this.editForm.zapisy = allZapisy;
      },
      deep: true,
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 5.75vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0 0.2em 0.1em;
  margin: 0;
}

h5 {
  text-align: center;
  font-size: 2.8em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;
  /* 2.51563rem */
  margin: 0;
  margin-bottom: 0.3em;
}

darceky-table {
  font-size: 0.8vw;
}

.dropdown-pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1.2em;
  padding: 0.7em 0.5em 0.5em 0.5em;
  margin-top: 0.22em;
  background: none;
}

.dropdown-pagination .arrow-btn {
  background: #fef35a;
  color: #393939;
  border-radius: 50%;
  width: 2em;
  height: 2em;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.3em;
  font-weight: bold;
  box-shadow: 0 2px 7px #d4cf7e44;
  cursor: pointer;
  user-select: none;
  transition: background 0.15s, color 0.15s, box-shadow 0.15s;
}

.dropdown-pagination .arrow-btn:hover:not(.disabled) {
  background: #ffe45a;
  color: #222;
  box-shadow: 0 4px 18px #ccc54a30;
}

.dropdown-pagination .arrow-btn.disabled {
  opacity: 0.44;
  pointer-events: none;
  cursor: default;
}

.dropdown-pagination .page-info {
  font-size: 1em;
  color: #222;
  min-width: 3em;
  text-align: center;
}

.my-multiselect {
  position: relative;
}

.my-multiselect ::v-deep .multiselect__content-wrapper {
  position: absolute;
  left: 0;
  right: 0;
  z-index: 9999;
  background: #90ca50;
  box-shadow: inset -7px 5px 15px 0 rgba(0, 0, 0, 0.25),
    0 4px 4px 0 rgba(0, 0, 0, 0.25);
  border-radius: 1.1em;
  margin-top: 1em;
}

.my-multiselect ::v-deep .multiselect__tag {
  color: #111;
  padding: 0.18em 1.1em;
  font-weight: 600;
  font-size: 0.9em;
  margin-right: 0.55em;
  margin-bottom: 0.22em;
  display: inline-flex;
  align-items: center;
}

.my-multiselect ::v-deep .multiselect__tag:last-child {
  margin-right: 0;
}

.my-multiselect ::v-deep .multiselect__tag-icon {
  color: #222 !important;
  font-size: 1.18em !important;
  margin-left: 0.6em !important;
  cursor: pointer !important;
  transition: color 0.14s !important;
}

.my-multiselect ::v-deep .multiselect__tag-icon:hover {
  color: #c00 !important;
}

.my-multiselect ::v-deep .multiselect__option {
  cursor: pointer;
  transition: background 0.13s, color 0.13s;
}

.my-multiselect ::v-deep .multiselect__element {
  margin-block: 0.5em;
}

.my-multiselect ::v-deep .multiselect__option--highlight {
  background: #b1e163;
  color: #212121;
}

/* Ak chceš ešte výraznejší efekt, pridaj: */
.my-multiselect ::v-deep .multiselect__option:hover {
  background: #a5d85c;
  color: #1d1d1d;
}

.arrow svg {
  transition: transform 0.22s cubic-bezier(0.4, 1.5, 0.7, 1);
  transform: rotate(0deg);
}

.arrow.open svg {
  transform: rotate(360deg);
}

.table-top {
  border-radius: 2.1875em;
  background: #fef35a;
  box-shadow: 3px 3px 3px 0px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: row;
  align-items: flex-end;
  font-size: 1.1em;
  font-style: normal;
  font-weight: 400;
  letter-spacing: 0.1rem;
  padding: 0.4em 1em;
  border: none;
  justify-content: space-between;
  box-sizing: border-box;
  white-space: nowrap;
  z-index: 5;
  gap: 1%;
}

.table {
  font-size: 1.2em;
}

.table-top {
  font-size: 1.4em;
}

.table-actions {
  margin-top: 1em;
  margin-bottom: 1em;
}

.table-row {
  display: flex;
  flex-direction: row;
  align-items: flex-end;
  justify-content: space-between;
  white-space: nowrap;
  box-sizing: border-box;
  padding: 0.4em 1em;
  font-size: 1.1em;
  gap: 1%;
}

.info {
  display: none;
}

.table-top p,
.table-row p {
  text-align: left;
}

.table-top p:nth-child(1),
.table-row p:nth-child(1) {
  width: 20%;
}

.table-top p:nth-child(2),
.table-row p:nth-child(2) {
  width: 18%;
}

.table-top p:nth-child(3),
.table-row p:nth-child(3),
.table-top p:nth-child(5),
.table-row p:nth-child(5) {
  width: 12%;
}

.table-top p:nth-child(4),
.table-row p:nth-child(4) {
  width: 20%;
}

.table-top p:nth-child(7),
.table-row p:nth-child(7) {
  width: 9%;
}

.Bahnschrift {
  font-weight: 800;
  letter-spacing: 0.02em;
}

.Adumu {
  font-size: 110%;
}

//////////@at-root
///

.all-statistic {
  display: flex;
}

.darcekova-statistic {
  display: flex;
  justify-content: center;
  flex-direction: column;
  row-gap: 1.3em;
  margin: 1em auto 3em;
  align-items: center;
}

.hodnota {
  background-color: #ddd;
  color: black;
  font-weight: bold;
  border-radius: 8px;
  cursor: pointer;
  text-align: center;
  transition: background-color 0.3s, color 0.3s;
  font-size: 1.6em;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 2em;
  width: 3em;

  &:hover {
    transform: scale(1.1);
    transition-duration: 0.2s;
  }
}

.nadpis {
  font-size: 1.9em;
  font-weight: 900;
  margin: 1em auto 0em;
}

.podnadpis {
  font-size: 1.6em;
  margin: 0.5em auto;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: min-content;
}

.box-statistic {
  display: flex;
  justify-content: space-around;
  max-width: 40em;
  margin: auto;
  min-width: 25em;
}

.button {
  font-size: 1.2em;
  margin: auto;
}

.generovanie {
  display: flex;
  justify-content: center;
  gap: 5%;
  flex-wrap: wrap;
  align-items: center;
  margin: 2em auto;
  font-size: 0.9em;
  row-gap: 1.5em;
}

input,
textarea,
select,
.my-multiselect {
  border-radius: 1.0625em;
  background: #90ca50;
  box-shadow: -7px 5px 15px 0px rgba(0, 0, 0, 0.25) inset,
    0px 4px 4px 0px rgba(0, 0, 0, 0.25);
  padding: 0.35em 5%;
  width: 12em;
  box-sizing: border-box;
}

select,
.my-multiselect {
  width: 16em;
}

label {
  font-size: 1.6em;
}

.generovanie .hodnota {
  height: 1.9em;
  width: 3em;
  margin: auto;
}

.row-form {
  display: flex;
  flex-direction: column;
  gap: 1em;
}

// TABUĽKA
.table {
  overflow-x: auto;
  padding: 0.5em 0;
}

.table-top,
.table-row {
  display: grid;
  align-items: center;
  gap: 0.5em;
  justify-items: start;
  gap: 0.5vw;
  /* nastavíme sloupce na FR jednotky s minmax, aby každý stĺpec
     dostal primeraný pomer miesta */
  grid-template-columns:
    minmax(10em, 2fr)
    /* Email – dostane viac miesta */
    minmax(10em, 1.2fr)
    /* Kód poukážky */
    minmax(11em, 1fr)
    /* Zakúpené (timestamp) */
    minmax(11em, 2fr)
    /* Využil email – dosť miesta na viaceré emaily */
    minmax(7em, 1fr)
    /* Platnosť */
    minmax(5em, 0.6fr);
  // grid-template-columns:
  //   minmax(190px, 2fr)
  //   /* Email – dostane viac miesta */
  //   minmax(120px, 1.2fr)
  //   /* Kód poukážky */
  //   minmax(140px, 1fr)
  //   /* Zakúpené (timestamp) */
  //   minmax(190px, 2fr)
  //   /* Využil email – dosť miesta na viaceré emaily */
  //   minmax(80px, 1fr)
  //   /* Platnosť */
  //   minmax(70px, 0.6fr);
  white-space: normal;
}

.table-row span,
.table-row p {
  overflow-wrap: anywhere;
  word-break: break-word;
  white-space: nowrap;
}

/* hlavičky nech sú vždy celé */
.table-top p {
  white-space: nowrap;
  overflow: visible;
  text-overflow: clip;
}

.edit-actions select {
  max-width: 16em;
}

.dropdown-wrapper {
  position: relative;
  display: inline-block;
}

/* dropdown tlačidlá */
.dropdown-btn {
  @extend .button;
  padding: 0.4em 1em;
  font-size: 1em;
}

/* popover */
.picker-popover {
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  background: #fffdf2;
  border: 1px solid rgba(254, 243, 90, 0.6);
  border-radius: 0.5em;
  padding: 1em;
  box-shadow: 0 2px 7px rgba(0, 0, 0, 0.1);
  z-index: 100;
  margin-top: 0.5em;

  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75em;
  text-align: center;
  max-height: 30vh;
  overflow-y: auto;
}

.picker-popover h5 {
  margin: 0;
  font-size: 1em;
  font-weight: bold;
}

.picker-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  grid-template-columns: auto auto;
  justify-content: center;
  align-items: center;
  gap: 0.4em 1em;
  width: 100%;
  max-height: 12em;
  overflow-y: auto;
}

.picker-list label {
  display: flex;
  align-items: center;
  gap: 0.5em;
  font-size: 0.9em;
}

.picker-popover .button.green {
  margin-top: 0.5em;
  padding: 0.5em 1.2em;
}

.table-top {
  background: #fef35a;
  font-weight: 700;
  font-size: 1.11em;
  padding: 0.35em 0.8em;
  margin-bottom: 0;
  box-shadow: 0 2px 7px rgba(254, 243, 90, 0.5);
}

.table-row {
  font-weight: 400;
  font-size: 1em;
  padding: 0.16em 0.8em;
  transition: background 0.14s;
}

.table-row:last-child {
  border-bottom: none;
}

.table-row:hover {
  background: rgba(254, 243, 90, 0.5);
}

.table-row input.item,
.table-row select.item,
.table-row .item.multiselect {
  background: #fffdf2;
  border: 1.2px solid rgba(254, 243, 90, 0.5);
  border-radius: 0.65em;
  padding: 0.21em 0.6em;
  font-size: 1em;
  font-weight: 500;
  color: #222;
  box-shadow: 0 1.5px 7px #ffeec755 inset;
  margin-bottom: 0;
  width: 100%;
}

/* aby multiselect placeholder a tagy tiež pekne sedeli */
.table-row .item.multiselect ::v-deep(.multiselect__tags) {
  padding: 0.2em 0.6em;
  min-height: 2em;
}

.table-row .item.multiselect ::v-deep(.multiselect__placeholder) {
  color: #666;
  font-style: italic;
}

input.item:focus select.item:focus {
  border: 1.3px solid #c8db5e;
  background: #fffde0;
  outline: none;
}

.table-row span,
.table-row input,
.table-row p {
  text-align: left;
  margin: 0;
  padding: 0 0.14em;
  font-size: 1em;
}

.table-row span:last-child,
.table-row input:last-child {
  text-align: right;
}

.table {
  overflow-x: auto;
  max-width: 100vw;
  min-width: 100%;
  font-size: 90%;
}

.table-row.edit-mode {
  display: flex;
  flex-direction: row;
  align-items: center;
  flex-wrap: wrap; // pri menšom mieste pôjde pod seba
  gap: 0.5em;
  position: relative;
  animation: edit-fade-in 0.26s cubic-bezier(0.7, 1.5, 0.4, 1);
  padding-block: 1em;

  .item {
    flex: 1 1 0px;
    min-width: 110px;
    margin-bottom: 0;
  }

  .table-row .item.multiselect,
  .table-row select.item {
    background: #fffdf2;
    border: 1.2px solid rgba(254, 243, 90, 0.5);
    border-radius: 0.65em;
    padding: 0.21em 0.6em;
    font-size: 1em;
    font-weight: 500;
    color: #222;
    box-shadow: inset 0 1.5px 7px #ffeec755;
    width: 100%;
    box-sizing: border-box;
    margin-bottom: 0;
  }

  /* 2) Vnútorné elementy multiselectu zarovnáme a nastavíme placeholder */
  .table-row .item.multiselect ::v-deep(.multiselect__tags),
  .table-row .item.multiselect ::v-deep(.multiselect__tags-wrap) {
    min-height: 2em;
    padding: 0.2em 0.6em;
  }

  .table-row .item.multiselect ::v-deep(.multiselect__placeholder) {
    color: #666;
    font-style: italic;
  }

  /* 3) Keď sa otvorí dropdown, nech má rovnakú farbu pozadia a tieň */
  .table-row .item.multiselect ::v-deep(.multiselect__content-wrapper) {
    background: #fffdf2;
    box-shadow: inset 0 1.5px 7px #ffeec755, 0 2px 7px rgba(254, 243, 90, 0.5);
  }

  /* 4) Tiež nech checkboxy/označené tagy vyzerajú ako malé inputy */
  .table-row .item.multiselect ::v-deep(.multiselect__tag) {
    background: #fffde0;
    border: 1px solid #c8db5e;
    border-radius: 0.5em;
    padding: 0.2em 0.6em;
    color: #222;
  }

  /* 5) Na hover/focus efekt */
  .table-row .item.multiselect:hover,
  .table-row select.item:hover,
  .table-row .item.multiselect:focus-within,
  .table-row select.item:focus {
    filter: brightness(0.95);
  }

  /* 6) Pridáme trochu medzeru nad edit-actions, aby to nebolo natlačené na selekty */
  .table-row.edit-mode .edit-actions {
    margin-top: 1em;
  }

  .edit-actions {
    width: 100%;
    display: flex;
    justify-content: center;
    gap: 1.5em;
    margin-top: 0.5em;

    .button {
      min-width: 120px;
      font-size: 1em;
      padding: 0.5em 1.2em;
      border-radius: 2em;
      font-weight: bold;
      cursor: pointer;
      transition: filter 0.2s, background 0.18s;

      &.green {
        background: #90ca50;
        color: #fff;
        justify-content: center;
      }

      &.yellow {
        background: #fdd835;
        color: #fff;
        justify-content: center;
      }

      &.red {
        background: #f86e5c;
        color: #ffff;
        justify-content: center;
      }

      &:hover {
        filter: brightness(0.94) saturate(1.05);
      }
    }
  }
}

/* --- overlay (nezmenené) --- */
.invalid-dialog .dialog-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  z-index: 999;
}

/* --- samotný dialóg --- */
.invalid-dialog .dialog {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #fff;
  border: 1px solid #888;
  /* jemný šedý rám */
  border-radius: 6px;
  /* mierne zaoblené rohy */
  padding: 1.2em 1.5em;
  /* útulnejší vnútorný okraj */
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
  z-index: 1000;
  max-width: 320px;
  /* ako natívny alert */
  line-height: 1.4;
  font-family: system-ui, sans-serif;
  text-align: center;
}

/* nadpis + text */
.invalid-dialog .dialog p {
  margin: 0 0 1em 0;
  font-size: 1em;
  color: #333;
}

/* tlačidlá vedľa seba */
.invalid-dialog .dialog .button {
  display: inline-block;
  min-width: 120px;
  margin: 0.2em;
  padding: 0.5em 1em;
  font-size: 1em;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: filter 0.1s;
}

/* zelené "Pokračovať" */
.invalid-dialog .dialog .button.green {
  background: #4caf50;
  color: #fff;
}

/* červené "Zrušiť" */
.invalid-dialog .dialog .button.red {
  background: #f44336;
  color: #fff;
}

/* hover efekt */
.invalid-dialog .dialog .button:hover {
  filter: brightness(1.1);
}

// Animácia pre fade-in (pri prepnutí do edit-módu)
@keyframes edit-fade-in {
  from {
    opacity: 0;
    transform: translateY(-18px) scale(0.98);
  }

  to {
    opacity: 1;
    transform: none;
  }
}

.table-row.edit-mode input,
.table-row.edit-mode button {
  filter: brightness(1.04);
  box-shadow: 0 1px 7px #ffeec755 inset;
}

@media (max-width: 1024px) {
  .all-statistic {
    flex-direction: column;
  }

  label {
    margin-top: 0.5em;
  }

  .table-row.edit-mode {
    flex-direction: column;
    align-items: stretch;

    .item {
      width: 100%;
      min-width: unset;
      margin-bottom: 0.12em;
    }

    .edit-actions {
      flex-direction: column;
      align-items: center;
      gap: 0.8em;

      .button {
        width: 100%;
        min-width: 90px;
      }
    }
  }

  .edit-actions select {
    max-width: 100%;
  }
}

@media (max-width: 800px) {
  .table-top,
  .table-row {
    font-size: 0.98em;
    min-width: 470px;
    grid-template-columns: 1fr 1.3fr 0.9fr 1.1fr 0.95fr 0.6fr;
    padding: 0.12em 0.4em;
  }
}

@media (max-width: 650px) {
  .generovanie {
    font-size: 0.98em;
    gap: 0.6em;
    padding: 0.5em 0.5em 0.3em 0.5em;
  }

  .row-form {
    min-width: 120px;
  }

  .table-top,
  .table-row {
    font-size: 0.8em;
    min-width: 300px;
    grid-template-columns: 1.4fr 1.5fr 1fr 1fr 1fr 0.7fr;
    padding: 0.06em 0.11em;
  }

  .table-top {
    padding: 0.09em 0.09em;
  }
}
</style>
