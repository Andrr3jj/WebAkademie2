<template>
  <component
    :is="currentComponent"
    :user-info="userInfo"
    v-bind="componentProps"
  />
</template>

<script>
import UserView from "@/components/ucebna/ClassroomNormal.vue";
import StudentView from "@/components/ucebna/ClassroomStudent.vue";
import ParentView from "@/components/ucebna/ClassroomParent.vue";

export default {
  components: { UserView, StudentView, ParentView },
  computed: {
    userInfo() {
      return this.$store?.state?.user || {};
    },
    isParent() {
      const parentArr = this.userInfo?.edupage_registration_parent;
      return Array.isArray(parentArr) && parentArr.length > 0;
    },
    isStudent() {
      const studentObj = this.userInfo?.edupage_registration_student;
      return studentObj && Object.keys(studentObj).length > 0;
    },
    currentComponent() {
      if (this.isStudent) {
        return "StudentView";
      }
      if (this.isParent) {
        return "ParentView";
      }
      return "UserView";
    },
    componentProps() {
      // Ak je parent aj student, pošli hasStudent: true
      if (this.isStudent) {
        return { hasParent: this.isParent };
      }
      return {};
    },
  },
};
</script>
