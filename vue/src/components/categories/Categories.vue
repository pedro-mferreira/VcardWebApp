<template>
  <confirmation-dialog
    ref="confirmationDialog"
    confirmationBtn="Delete category"
    :msg="`Do you really want to delete the category ${categoryToDeleteDescription}?`"
    @confirmed="deleteConfirmed"
  >
  </confirmation-dialog>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">My Categories</h3>
    </div>
  </div>
  <hr />
  <div class="mx-2 mt-2">
    <button
      type="button"
      class="btn btn-success px-4 btn-addtask"
      @click="addCategory"
    >
      <i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add Category
    </button>
  </div>
  <category-table
    :categories="this.categories"
    @edit="editCategory"
    @delete="deleteCategory"
  >
  </category-table>
</template>

<script>
import CategoryTable from "./CategoryTable.vue";

export default {
  name: "Categories",
  components: {
    CategoryTable,
  },

  data() {
    return {
      username: this.$store.state.user.username,
      categories: [],
      categoryToDelete: null,
    };
  },
  computed:{
   categoryToDeleteDescription () {
      return this.categoryToDelete
        ? `#${this.categoryToDelete.id} (${this.categoryToDelete.name})`
        : ''
    } 
  },

  methods: {
    loadCategories() {
      this.$axios
        .get("/vcards/" + this.username + "/categories")
        .then((response) => {
          console.log(response.data.data);
          this.categories = response.data.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },

    editCategory(category) {
      console.log(category);
      this.$router.push({
        name: "Category",
        params: { id: category.id },
      });
    },

    addCategory() {
      this.$router.push({ name: "NewCategory" });
    },

    deleteConfirmed() {
      this.$axios
        .delete("categories/" + this.categoryToDelete.id)
        .then((response) => {
          let deletedCategory = response.data.data;
          this.$store.commit("removeCategory", deletedCategory);
          console.log(deletedCategory)
          let idx = this.categories.findIndex(
            (t) => t.id === deletedCategory.id
          );
          if (idx >= 0) {
            this.categories.splice(idx, 1);
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    deleteCategory(category) {
      this.categoryToDelete = category;
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