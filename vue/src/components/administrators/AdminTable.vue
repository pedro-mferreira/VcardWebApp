<template>
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th v-if="showDeleteButton"></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="admin in admins" :key="admin.id">
        <td>{{ admin.name }}</td>
        <td>{{ admin.email }}</td>

        <td class="text-end" v-if="showDeleteButton">
          <div class="d-flex justify-content-end">
            <button
              class="btn btn-xs btn-light"
              @click="deleteClick(admin)"
              v-if="showDeleteButton"
            >
              <i class="bi bi-xs bi-x-square-fill"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  name: "AdminTable",
  props: {
    admins: {
      type: Array,
      default: () => [],
    },
    showDeleteButton: {
      type: Boolean,
      default: true,
    },
  },
  emits: ["delete"],
  methods: {
    deleteClick(admin) {
      this.$emit("delete", admin);
    },
  },
};
</script>

<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}
</style>
