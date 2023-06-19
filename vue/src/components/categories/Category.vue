<template>
  <confirmation-dialog
    ref="confirmationDialog"
    confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!"
    @confirmed="leaveConfirmed"
  >
  </confirmation-dialog>
  <category-detail
    :operationType="operation"
    :category="category"
    :errors="errors"
    @save="save"
    @cancel="cancel"
  ></category-detail>
</template>

<script>
import CategoryDetail from "./CategoryDetail.vue";

export default {
  name: "Category",
  components: {
    CategoryDetail,
  },
  props: {
    id: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      category: this.newCategory(),
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
    newCategory() {
      return {
        id: null,
        name: "",
        type: "",
      };
    },
    loadCategory(id) {
      this.errors = null;
      if (!id || id < 0) {
        this.category = this.newCategory();
        this.originalValueStr = this.dataAsString();
      } else {
        this.$axios
          .get("categories/" + id)
          .then((response) => {
            console.log(response.data.data)
            this.category = response.data.data;
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
        this.category.vcard = this.$store.state.user.username;
        console.log("criada com vcar: " + this.category.vcard)
        this.$axios
          .post("categories", this.category)
          .then((response) => {
            this.$toast.success(
              "Category #" +
                response.data.data.id +
                " was created successfully."
            );
            this.category = response.data.data;
            this.originalValueStr = this.dataAsString();
            this.$store.commit("setCategory", this.category);
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
        let phone_number = this.category.vcard.phone_number;
        this.category.vcard = phone_number
        this.$axios
          .put("categories/" + this.id, this.category)
          .then((response) => {
            this.$toast.success(
              "Category #" +
                response.data.data.id +
                " was updated successfully."
            );
            this.category = response.data.data;
            this.originalValueStr = this.dataAsString();
            this.$store.commit("updateCategory", this.category);
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
