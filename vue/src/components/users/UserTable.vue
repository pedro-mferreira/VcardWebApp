<template>
  <table class="table">
    <thead>
      <tr>
        <th
          v-if="showId"
          class="align-middle"
        >#</th>
        <th
          v-if="showPhoto"
          class="align-middle"
        >Photo</th>
        <th class="align-middle">Name</th>
        <th
          v-if="showEmail"
          class="align-middle"
        >Email</th>
        <th
          v-if="showAdmin"
          class="align-middle"
        >Admin?</th>
        <th
          v-if="showGender"
          class="align-middle"
        >Gender</th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="user in users"
        :key="user.id"
      >
        <td
          v-if="showId"
          class="align-middle"
        >{{ user.id }}</td>
        <td
          v-if="showPhoto"
          class="align-middle"
        >
          <img
            :src="photoFullUrl(user)"
            class="rounded-circle img_photo"
          >
        </td>
        <td class="align-middle">{{ user.name }}</td>
        <td
          v-if="showEmail"
          class="align-middle"
        >{{ user.email }}</td>
        <td
          v-if="showAdmin"
          class="align-middle"
        >{{ user.type == 'A' ? 'Sim' : ''}}</td>
        <td
          v-if="showGender"
          class="align-middle"
        >{{ user.gender_name }}</td>
        <td
          class="text-end align-middle"
          v-if="showEditButton"
        >
          <div class="d-flex justify-content-end">
            <button
              class="btn btn-xs btn-light"
              @click="editClick(user)"
              v-if="showEditButton"
            ><i class="bi bi-xs bi-pencil"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {

  name: "UserTable",
  props: {
    users: {
      type: Array,
      default: () => [],
    },
    showId: {
      type: Boolean,
      default: true,
    },
    showEmail: {
      type: Boolean,
      default: true,
    },
    showAdmin: {
      type: Boolean,
      default: true,
    },
    showGender: {
      type: Boolean,
      default: false,
    },
    showPhoto: {
      type: Boolean,
      default: true,
    },
    showEditButton: {
      type: Boolean,
      default: true,
    },
  },
  emits: [
    'edit',
  ],
  methods: {
    photoFullUrl (user) {
      return user.photo_url
        ? this.$serverUrl + "/storage/fotos/" + user.photo_url
        : "./assets/img/avatar-none.png"
    },
    editClick (user) {
      this.$emit('edit', user)
    },
  }
}
</script>

<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}

.img_photo {
  width: 3.2rem;
  height: 3.2rem;
}
</style>
