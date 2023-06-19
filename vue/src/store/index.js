import { createStore } from 'vuex'
import axios from 'axios'

export default createStore({
  state: {
    user: null,
    transaction: [],
    categories: [],
    debitCategories: [],
    creditCategories: [],
    vcard: null,
    detailTransaction: null,
    errors: [],
    vcardToEdit: null,
    transactionsPagesInfo: {},
    vcardCategories: null,
  },
  mutations: {
    resetUser(state) {
      state.user = null;
    },
    setUser(state, loggedInUser) {
      state.user = loggedInUser

    },
    updateUser(state, updateUser) {
      state.user = updateUser
    },
    setTransaction(state, transaction) {
      state.transaction = transaction;
    },
    setCategories(state, categories) {
      state.categories = categories;
    },
    setDebitCategories(state, categories) {
      state.debitCategories = categories;
    },
    setCreditCategories(state, categories) {
      state.creditCategories = categories;
    },
    setCategory(state, category) {
      state.categories.push(category);
    },
    updateCategory(state, category) {
      let oldCat = state.categories.findIndex(o => o.id === category.id)
      state.categories.splice(oldCat, 1);
      state.categories.push(category)
    },
    removeCategory(state, category) {
      let oldCat = state.categories.findIndex(o => o.id === category.id)
      state.categories.splice(oldCat, 1);
    },
    setVcard(state, vcard) {
      state.vcard = vcard;
    },
    setDetailTransaction(state, transaction) {
      state.detailTransaction = transaction;
    },
    updateCurrentVcardBalance(state, newBalance) {
      state.vcard.balance = newBalance;
    },
    setErrors(state, errors) {
      state.errors = errors;
    },
    setVcardToEdit(state, vcard) {
      state.vcardToEdit = vcard;
    },
    setTransactionsPagesInfo(state, info) {
      state.transactionsPagesInfo = info;
    },
    setVcardCategories(state, categories) {
      state.vcardCategories = categories;
    },


  },
  actions: {
    async register(context, vcardUser) {
      try {
        //register
        let formData = new FormData();
        formData.append("photo_url", vcardUser.photo_url);
        formData.append("phone_number", vcardUser.phone_number);
        formData.append("password", vcardUser.password);
        formData.append("name", vcardUser.name);
        formData.append("confirmation_code", vcardUser.confirmation_code);
        formData.append("email", vcardUser.email);

        await axios.post('/vcards', formData)
      } catch (error) {
        console.log("error")
        throw error
      }

      //login para obter token
      var credentials = { username: "", password: "" }
      credentials.username = vcardUser.phone_number
      credentials.password = vcardUser.password
      await context.dispatch('login', credentials);

    },
    async login(context, credentials) {
      try {
        let response = await axios.post('login', credentials)
        axios.defaults.headers.common.Authorization =
          "Bearer " + response.data.access_token;
        response = await axios.get('users/me');
        context.commit('setUser', response.data.data);
        if (response.data.data.user_type == 'V') {
          context.dispatch('getCategories', response.data.data.username);
          context.dispatch('getVcard', response.data.data.username);
        }

        //meter so este a mandar
        this.$socket.emit('logged_in', context.state.user)

      } catch (error) {
        delete axios.defaults.headers.common.Authorization;
        context.commit('resetUser', null);
        throw error
      }
    },
    async logout(context) {
      try {
        await axios.post('logout');
      }
      finally {

        this.$socket.emit('logged_out', context.state.user)
        delete axios.defaults.headers.common.Authorization;
        context.commit('resetUser', null);
      }
    },
    async getCategories(context, vcard) {
      try {
        let response = await axios.get('/vcards/' + vcard + '/categories');
        let categories = response.data.data;
        let debitCategories = [];
        let creditCategories = [];
        for (var i = 0; i < categories.length; i++) {
          if (categories[i].type == "D") {
            debitCategories.push(categories[i]);
          }
          if (categories[i].type == "C") {
            creditCategories.push(categories[i]);
          }
        }
        context.commit('setDebitCategories', debitCategories);
        context.commit('setCreditCategories', creditCategories);
        context.commit('setCategories', categories);
      } catch (error) {
        console.log("error");
        throw error;
      }
    },
    async loadUserAfterChange(context) {
      let response = await axios.get('users/me')
      context.commit('setUser', response.data.data)

    },
    async getVcard(context, vcard) {
      try {
        let response = await axios.get('/vcards/' + vcard);

        context.commit('setVcard', response.data.data);

      } catch (error) {
        console.log("error")
        throw error;
      }
    },
    async getVcardCategories(context, vcard) {
      try {
        let response = await axios.get('/vcards/' + vcard + "categories");
        context.commit('setVcardCategories', response.data.data);

      } catch (error) {
        console.log("error");
        throw error;
      }
    },
    async loadTransactions(context, vcard) {
      try {
        let response = await axios.get("/vcards/" + vcard + "/transactions");
        let transactions = response.data.data;
        // //Ordena da mais recente para a mais antiga
        transactions = transactions.sort(
          (a, b) => new Date(b.datetime) - new Date(a.datetime)
        );
        context.commit('setTransaction', transactions);

      }
      catch (error) {
        console.log(error)
        this.$toast.error("Sorry! Transactions Temporarily Unavaible");
      }
    }
  }
})