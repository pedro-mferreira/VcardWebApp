<template>
  <confirmation-dialog
    ref="confirmationDialog"
    confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!"
    @confirmed="leaveConfirmed"
  >
  </confirmation-dialog>
  <default-category-detail
    :operationType="operation"
    :category="defaultCategory"
    :errors="errors"
    @save="save"
    @cancel="cancel"
  ></default-category-detail>
</template>

<script>
import DefaultCategoryDetail from "./DefaultCategoryDetail.vue";

export default {
  name: "DefaultCategory",
  components: {
    DefaultCategoryDetail,
  },
  props: {
    id: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      defaultCategory: this.newDefaultCategory(),
      errors: null,
    };
  },
  computed: {
    operation() {
      return !this.id || this.id < 0 ? "insert" : "update";
    },
  },
  watch: {
    // beforeRouteUpdate was not fired correctly
    // Used this watcher instead to update the ID
    id: {
      immediate: true,
      handler(newValue) {
        this.loadCategory(newValue);
      },
    },
  },
  methods: {
    dataAsString() {
      return JSON.stringify(this.category);
    },
    newDefaultCategory() {
      return {
        id: null,
        name: "",
        type: "",
      };
    },
    loadCategory(id) {
      this.errors = null;
      if (!id || id < 0) {
        this.defaultCategory = this.newDefaultCategory();
        this.originalValueStr = this.dataAsString();
      } else {
        this.$axios
          .get("defaultCategories/" + id)
          .then((response) => {
            console.log(response.data.data)
            this.defaultCategory = response.data.data;
            this.originalValueStr = this.dataAsString();
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    save() {
      this.errors = null;
      if (this.operation == "insert") {
        this.$axios
          .post("defaultCategories", this.defaultCategory)
          .then((response) => {
            this.$toast.success(
              "Default Category #" +
                response.data.data.id +
                " was created successfully."
            );
            this.defaultCategory = response.data.data;
            this.originalValueStr = this.dataAsString();
            this.$router.back();
          })
          .catch((error) => {
            if (error.response.status == 422) {
              this.$toast.error(
                "Category was not created due to validation errors!"
              );
              this.errors = error.response.data.errors;
            } else {
              this.$toast.error(
                "Category was not created due to unknown server error!"
              );
            }
          });
      } else {
        this.$axios
          .put("defaultCategories/" + this.id, this.defaultCategory)
          .then((response) => {
            this.$toast.success(
              "Default Category #" +
                response.data.data.id +
                " was updated successfully."
            );
            this.defaultCategory = response.data.data;
            this.originalValueStr = this.dataAsString();
            this.$router.back();
          })
          .catch((error) => {
            if (error.response.status == 422) {
              this.$toast.error(
                "Category #" +
                  this.id +
                  " was not updated due to validation errors!"
              );
              this.errors = error.response.data.errors;
            } else {
              this.$toast.error(
                "Category #" +
                  this.id +
                  " was not updated due to unknown server error!"
              );
            }
          });
      }
    },
    cancel() {
      // Replace this code to navigate back
      // this.loadProject(this.id)
      this.$router.back();
    },
    leaveConfirmed() {
      if (this.nextCallBack) {
        this.nextCallBack();
      }
    },
  },
  beforeRouteLeave(to, from, next) {
    this.nextCallBack = null;
    let newValueStr = this.dataAsString();
    if (this.originalValueStr != newValueStr) {
      this.nextCallBack = next;
      let dlg = this.$refs.confirmationDialog;
      dlg.show();
    } else {
      next();
    }
  },
};
</script>
