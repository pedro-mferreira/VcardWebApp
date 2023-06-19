<template>
  <!-- Button trigger to Show modal - HIDDEN -->
  <button
    ref="hiddenButtonToShowDialog"
    type="button"
    class="d-none"
    data-bs-toggle="modal"
    data-bs-target="#confirmationModalId"
  >
  </button>

  <!-- Modal -->
  <div
    class="modal fade"
    id="confirmationModalId"
    tabindex="-1"
    aria-labelledby="confirmationModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog">
      <!-- Button trigger to Hide modal - HIDDEN -->
      <button
        ref="hiddenButtonToHideDialog"
        type="button"
        class="d-none"
        data-bs-dismiss="modal"
      >
      </button>

      <div class="modal-content">
        <div class="modal-header">
          <h5
            class="modal-title"
            id="confirmationModalLabel"
          >{{ title }}</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div style="padding-left:5px;padding-right:5px;">
          <label for="inputconfirmation_code" class="form-label">Confirmation Code(PIN)</label>
        <input
          type="password"
          maxlength="4"
          class="form-control"
          id="inputconfirmation_code"
          required
          v-model="pinConfirm"
        />
        <label for="inputPassword" class="form-label">Password</label>
        <input
          type="password"
          class="form-control"
          id="inputPassword"
          required
          v-model="passwordConfirm"
        />
        </div>
        <div class="modal-body">
          {{ msg }}
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            data-bs-dismiss="modal"
          >{{ cancelBtn }}</button>
          <button
            type="button"
            class="btn btn-primary"
            @click="clickConfirm"
          >{{ confirmationBtn }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ConfirmationDialog',
  props: {
    cancelBtn: {
      type: String,
      default: 'Cancel'
    },
    confirmationBtn: {
      type: String,
      default: 'Confirm'
    },
    title: {
      type: String,
      default: 'Confirmation'
    },
    msg: {
      type: String,
      default: ''
    },
    errorMessage: {
      type: Object,
      default: null,
    }
  },
  emits: [
    'show',
    'hide',
    'confirmed'
  ],
  data() {
    return {
      pinConfirm:"",
      passwordConfirm:"",
    };
  },
  methods: {
    show () {
      //Easy way to show the modal:
      let btnToShowModal = this.$refs.hiddenButtonToShowDialog
      btnToShowModal.click()
      this.$emit('show')
    },
    hide () {
      //Easy way to hide the modal:
      let btnToHideModal = this.$refs.hiddenButtonToHideDialog
      btnToHideModal.click()
      this.$emit('hide')
    },
    clickConfirm () {
      this.hide()
      this.$emit('confirmed', this.pinConfirm,this.passwordConfirm)
 
    },
  }
}
</script>
