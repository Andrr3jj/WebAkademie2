<template>
  <section v-if="isLoggedIn && !isMobile" class="computer" :id="$route.name">
    <div class="scroll">
      <div class="content">
        <HeadlineSection
          title="Online prihláška"
          subtitle="Pre žiakov do Heligónkovej Akadémie"
          text="Vyplnením tejto prihlášky zapíšete seba alebo svoje dieťa na kurzy heligónky."
        />
        <ProgressBar
          :current-step="currentStep"
          :sub-step="currentSubStep"
          :form-data="formData"
          :attempt="attempt"
          :submitted="submissionDone"
          :submit-error="submitError"
          @update-step="updateStepData"
          @selected-role="handleRole"
          @step-valid="onStepValid"
          @restart="restartForm"
        />
        <FooterSection
          :current-step="currentStep"
          :total-steps="effectiveSteps"
          :sub-step="currentSubStep"
          :role="formData[0].role"
          :is-step-valid="isStepValid"
          @back="prev"
          @next="next"
          @form-submit="handleSubmit"
          :is-submitted="submissionDone"
        />
      </div>
    </div>
  </section>

  <div v-else-if="isLoggedIn && isMobile" class="mobile" :id="$route.name">
    <section>
      <div class="scroll">
        <HeadlineSection
          title="Online prihláška"
          subtitle="Pre žiakov do Heligónkovej Akadémie"
          text="Vyplnením tejto prihlášky zapíšete seba alebo svoje dieťa na kurzy heligónky."
        />
        <ProgressBar
          :current-step="currentStep"
          :sub-step="currentSubStep"
          :form-data="formData"
          :attempt="attempt"
          :submitted="submissionDone"
          :submit-error="submitError"
          @update-step="updateStepData"
          @selected-role="handleRole"
          @step-valid="onStepValid"
          @restart="restartForm"
        />
        <FooterSection
          :current-step="currentStep"
          :total-steps="effectiveSteps"
          :sub-step="currentSubStep"
          :role="formData[0].role"
          :is-step-valid="isStepValid"
          @back="prev"
          @next="next"
          @form-submit="handleSubmit"
          :is-submitted="submissionDone"
        />
      </div>
    </section>
  </div>
</template>

<script>
import axios from "axios";
import HeadlineSection from "@/components/Edupage/Prihlaska/HeadlineSection.vue";
import ProgressBar from "@/components/Edupage/Prihlaska/ProgressBar.vue";
import FooterSection from "@/components/Edupage/Prihlaska/FooterSection.vue";

export default {
  name: "PrihlaskaPage",
  components: { HeadlineSection, ProgressBar, FooterSection },

  data() {
    return {
      currentStep: 0,
      currentSubStep: 0,
      attempt: false,
      isStepValid: false,
      isMobile: window.innerWidth <= 768,
      submissionDone: false,
      submitError: "",
      redirectTimer: null,
      formData: [
        { role: "" },
        {
          name: "",
          surname: "",
          dateOfBirth: "",
          placeOfBirth: "",
          address: "",
          experience: "",
          email: "",
          phone: "",
        },
        { placeOfStudy: "" },
        { formOfStudy: "" },
        { note: "", lessonTime: "", termsAccepted: false },
      ],
    };
  },

  computed: {
    effectiveSteps() {
      return 5;
    },
    currentRole() {
      return this.formData[0]?.role;
    },
    isLoggedIn() {
      return this.$store.state.user != null;
    },
  },

  watch: {
    isLoggedIn(val) {
      if (!val) {
        this.$router.replace({
          path: "/potrebne-prihlasenie",
          query: { redirect: this.$route.fullPath },
        });
      }
    },
  },

  mounted() {
    window.addEventListener("resize", this.checkMobile);
    this.redirectTimer = setTimeout(() => {
      if (!this.isLoggedIn) {
        this.$router.replace({
          path: "/potrebne-prihlasenie",
          query: { redirect: this.$route.fullPath },
        });
      }
    }, 400);
  },

  beforeUnmount() {
    window.removeEventListener("resize", this.checkMobile);
    if (this.redirectTimer) clearTimeout(this.redirectTimer);
  },

  methods: {
    checkMobile() {
      this.isMobile = window.innerWidth <= 750;
    },
    updateStepData(newFormDataArray) {
      this.formData = newFormDataArray;
    },
    handleRole(role) {
      if (this.formData[0].role !== role) {
        this.formData[0].role = role;
        this.formData[1] = {
          name: "",
          surname: "",
          dateOfBirth: "",
          placeOfBirth: "",
          address: "",
          experience: "",
          email: "",
          phone: "",
        };
        this.formData = JSON.parse(JSON.stringify(this.formData));
      }
    },
    onStepValid(val) {
      this.isStepValid = val;
    },
    restartForm() {
      this.currentStep = 0;
      this.currentSubStep = 0;
      this.attempt = false;
      this.isStepValid = false;
      this.submissionDone = false;
      this.submitError = "";
      this.formData = [
        { role: null },
        {
          name: "",
          surname: "",
          dateOfBirth: "",
          placeOfBirth: "",
          address: "",
          experience: "",
          email: "",
          phone: "",
        },
        { placeOfStudy: "" },
        { formOfStudy: "" },
        { note: "", lessonTime: "", termsAccepted: false },
      ];
    },
    next() {
      this.attempt = true;
      if (!this.isStepValid) return;
      if (this.currentStep < this.effectiveSteps - 1) {
        this.currentStep++;
        this.attempt = false;
        this.isStepValid = false;
      }
    },
    prev() {
      if (this.currentStep > 0) {
        for (let i = this.currentStep; i < this.formData.length; i++) {
          if (i === 0) this.formData[i] = { role: null };
          else if (i === 1)
            this.formData[i] = {
              name: "",
              surname: "",
              dateOfBirth: "",
              placeOfBirth: "",
              address: "",
              experience: "",
              email: "",
              phone: "",
            };
          else if (i === 2) this.formData[i] = { placeOfStudy: "" };
          else if (i === 3) this.formData[i] = { formOfStudy: "" };
          else if (i === 4)
            this.formData[i] = {
              note: "",
              lessonTime: "",
              termsAccepted: false,
            };
        }
        this.formData = JSON.parse(JSON.stringify(this.formData));
        this.currentStep--;
        this.attempt = false;
        this.isStepValid = false;
      }
    },
    buildPayload() {
      const [roleStep, person, study, form, last] = this.formData;
      const data = new FormData();
      data.append("registerer", roleStep?.role || "");
      data.append("name", person?.name || "");
      data.append("surname", person?.surname || "");
      data.append("placeOfBirth", person?.placeOfBirth || "");
      data.append("address", person?.address || "");
      data.append("dateOfBirth", person?.dateOfBirth || "");
      data.append("experience", person?.experience || "");
      data.append("email", person?.email || "");
      data.append("phone", person?.phone || "");
      data.append("placeOfStudy", study?.placeOfStudy || "");
      data.append("formOfStudy", form?.formOfStudy || "");
      data.append("note", last?.note || "");
      data.append("approximateTimeOfStudy", last?.lessonTime || "");
      data.append("termsAccepted", last?.termsAccepted ? "1" : "0");
      return data;
    },
    async handleSubmit() {
      this.submitError = "";
      this.attempt = true;
      const data = this.buildPayload();
      try {
        const res = await axios.post(
          "https://heligonkovaakademia.sk/api/edupage/register.php",
          data,
          { headers: { Accept: "application/json" } }
        );
        if (res.data.status === "Request succesfull") {
          this.submissionDone = true;
        } else {
          this.submissionDone = false;
          this.submitError =
            res.data?.data || "Odoslanie sa nepodarilo. Skúste to znova.";
        }
      } catch {
        this.submissionDone = false;
        this.submitError =
          "Odoslanie sa nepodarilo. Skúste to znova, alebo nás kontaktujte.";
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.content {
  display: flex;
  flex-direction: column;
  justify-content: space-around;
  height: 100%;
  gap: 3em;
}
</style>
