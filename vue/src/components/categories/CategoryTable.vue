<template>
  <table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">Type</th>
        <th scope="col">Name</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="category in categories" :key="category.id">
        <td v-if="category.type == 'C'">Credit</td>
        <td v-if="category.type == 'D'">Debit</td>
        <td>{{ category.name }}</td>

        <button class="btn btn-xs btn-light" @click="editCategory(category)">
          <i class="bi bi-xs bi-pencil"></i>
        </button>
        <button class="btn btn-xs btn-light" @click="deleteCategory(category)">
          <i class="bi bi-xs bi-x-square-fill"></i>
        </button>
      </tr>
    </tbody>
  </table>
</template>
<script>
export default {
  name: "CategoriesTable",
  props: {
    categories: {
      type: Array,
      default: () => [],
    },
  },
  emits: ["edit", "delete"],

  methods: {
    editCategory(category) {
      this.$emit("edit", category);
    },
    deleteCategory(category) {
      this.$emit("delete", category);
    },
  },
};
</script>

<style>
</style>




