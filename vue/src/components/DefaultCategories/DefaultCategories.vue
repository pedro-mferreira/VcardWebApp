<template>
  <confirmation-dialog
    ref="confirmationDialog"
    confirmationBtn="Delete category"
    :msg="`Do you really want to delete the category ${defaultCategoryToDeleteDescription}?`"
    @confirmed="deleteConfirmed"
  >
  </confirmation-dialog>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">Default Categories</h3>
    </div>
  </div>
  <hr />
  <div class="mx-2 mt-2">
    <button
      type="button"
      class="btn btn-success px-4 btn-addtask"
      @click="addDefaultCategory"
    >
      <i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add Default Category
    </button>
  </div>
  <default-category-table
    :categories="this.defaultCategories"
    @edit="editDefaultCategory"
    @delete="deleteDefaultCategory"
  >
  </default-category-table>
</template>

<script>
import DefaultCategoryTable from "./DefaultCategoryTable.vue";

export default {
  name: "DefaultCategories",
  components: {
    DefaultCategoryTable,
  },

  data() {
    return {
      defaultCategories: [],
      defaultCategoryToDelete: null,
    };
  },
  computed:{
   defaultCategoryToDeleteDescription () {
      return this.defaultCategoryToDelete
        ? `#${this.defaultCategoryToDelete.id} (${this.defaultCategoryToDelete.name})`
        : ''
    } 
  },

  methods: {
    loadCategories() {
      this.$axios
        .get("defaultCategories")
        .then((response) => {
          console.log(response.data.data);
          this.defaultCategories = response.data.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },

    editDefaultCategory(defaultCategory) {
      console.log(defaultCategory);
      this.$router.push({
        name: "DefaultCategory",
        params: { id: defaultCategory.id },
      });
    },

    addDefaultCategory() {
      this.$router.push({ name: "NewDefaultCategory" });
    },

    deleteConfirmed() {
      this.$axios
        .delete("defaultCategories/" + this.defaultCategoryToDelete.id)
        .then((response) => {
          let deletedCategory = response.data.data;
          console.log(deletedCategory)
          let idx = this.defaultCategories.findIndex(
            (t) => t.id === deletedCategory.id
          );
          if (idx >= 0) {
            this.defaultCategories.splice(idx, 1);
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    deleteDefaultCategory(category) {
      this.defaultCategoryToDelete = category;
      let dlg = this.$refs.confirmationDialog;
      dlg.show();
    },
  },

  mounted() {
    this.loadCategories();
  },
};
</script>

<style scoped>
.filter-div {
  min-width: 12rem;
}
.total-filtro {
  margin-top: 0.35rem;
}
.btn-addtask {
  margin-top: 1.85rem;
}
</style>