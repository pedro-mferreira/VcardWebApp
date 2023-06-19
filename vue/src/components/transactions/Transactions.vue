<template>
  <div>
    <div class="d-flex justify-content-between">
      <div class="mx-2">
        <h3 class="mt-4">My Transactions</h3>
      </div>
    </div>
    <div class="mx-2 total-filtro">
      <h5 class="mt-4">Total: {{ totalTransactions }}</h5>
    </div>
    <div class="d-flex justify-content-around">
      <div>
        <label for="selectPaymentType" class="form-label"
          >Filter by Payment Type:</label
        >
        <select
          id="selectPayment"
          class="form-select"
          v-model="filterByPaymentType"
        >
          <option :value="-1">Any</option>
          <option v-for="type in paymentTypes" :value="type" :key="type.id">
            {{ type.name }}
          </option>
        </select>
      </div>

      <div>
        <label for="selectPaymentType" class="form-label"
          >Filter by Category:</label
        >
        <select
          class="form-select"
          id="selectCategory"
          v-model="filterByCategory"
        >
          <option :value="{ id: -1 }">Any</option>
          <option
            v-for="item in this.$store.state.categories"
            :value="item"
            :key="item.category_id"
          >
            {{ item.name }}
          </option>
        </select>
      </div>

      <div v-if="filterByCategory.id==-1">
        <label for="selectPaymentType" class="form-label"
          >Filter by Type:</label>


        <select id="selectType" class="form-select" v-model="filterByType">
          <option value="-1" selected>Any</option>
          <option value="0">Credit</option>
          <option value="1">Debit</option>
        </select>
      </div>
       

      <div>
        <div class="mx-2 mt-2 flex-grow-1 filter-div">
          <label for="dateStart" class="form-label" style="display: block"
            >Filter by Date:</label
          >
          <small>StartDate:</small>
          <input id="dateStart" type="date" v-model="filterByDate.startDate" />
          <small>EndDate:</small>
          <input id="dateEnd" type="date" v-model="filterByDate.endDate" />
        </div>
      </div>
    </div>

    <div class="mb-3 d-flex justify-content-center">
      <button
        type="button"
        class="btn btn-success px-4 btn-addtask"
        @click="filter"
      >
        Filter
      </button>
    </div>
    <div class="mx-2">
      <h5 class="mt-4">Current Balance: {{ balance }}€</h5>
    </div>
    <hr />
    <div class="mx-2 mt-2">
      <button
        type="button"
        class="btn btn-success px-4 btn-addtask"
        @click="addTransaction"
      >
        <i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add Transaction
      </button>
    </div>
    <transaction-table
      :transactions="paginatedData"
      @edit="editTransaction"
    >
    </transaction-table>
    <PaginationNavBar :pageCount="pageCount" :size="pagesInfo.size" @currentPageNumber="changePage"></PaginationNavBar>
    <!-- <jw-pagination :items="this.currentTransactions" @changePage="onChangePage"></jw-pagination> -->
  </div>
</template>

<script>
// import PaginationTrasaction from './PaginationTrasaction.vue';
import TransactionTable from "./TransactionsTable.vue";
import PaginationNavBar from "./PaginationNavBar.vue";

export default {
  name: "Transactions",
  components: {
    TransactionTable,
    PaginationNavBar
  },

  data() {
    return {
      username: this.$store.state.user.username,
      //transactions with filters
      transactions: [],
      //transaction for final rendering table
      currentTransactions: [],
      transactionsByType: [],
      
      paymentTypes: [],
      balance: 0,
      oldBalance: 0,
      totalTransactions: 0,
      filterByPaymentType: -1,
      filterByType: -1,
      filterByCategory: { id: -1 },
      filterByDate: { startDate: "", endDate: "" },
      
      pagesInfo:{ 
        pageNumber:0,
        size:10,
      },
      
    };
  },

  computed:{
        pageCount(){ //numero de paginas
            let l = this.currentTransactions.length,
                s = this.pagesInfo.size;
            return Math.floor(l/s);
        },
        paginatedData(){
            const start = this.pagesInfo.pageNumber * this.pagesInfo.size, end = start + this.pagesInfo.size;
            return this.currentTransactions.slice(start, end);
        },
    },

  methods: {
    editTransaction(transaction) {
      this.$store.commit("setTransaction", transaction);
      this.$router.push({
        name: "Transaction",
        params: { id: transaction.id },
      });
    },

    addTransaction() {
      this.$router.push({ name: "NewTransaction" });
    },

    filter() {
      
      //iniciar sempre uma nova filtragem
      this.transactions = this.$store.state.transaction;

      if (this.filterByPaymentType != -1) {
        this.filterByPaymentTypeFunction();
      }
      if (this.filterByType != -1) {
        this.filterByTypeFunction();
      }

      if (
        this.filterByDate.startDate != "" &&
        this.filterByDate.endDate != ""
      ) {
        this.filterByDateFunction();
      } else if (
        this.filterByDate.startDate != "" ||
        this.filterByDate.endDate != ""
      ) {
        this.$toast.error("StartDate and EndDate  should be filled!");
      }

      if (this.filterByCategory.id != -1) {
        this.filterByCategoryFunction();
      }
      this.currentTransactions = this.transactions;
      this.totalTransactions = this.currentTransactions.length;
    },
    filterByPaymentTypeFunction() {
        this.transactions = this.transactions.filter((transaction) => {
        if (this.filterByPaymentType == -1) {
          return;
        }
        //return transaction.payment_type.id == this.filterByPaymentType.id;
        return transaction.payment_type.name == this.filterByPaymentType.name;
      });

     
    },
    filterByTypeFunction() {
      switch (this.filterByType) {
        case "0":
          this.transactions = this.transactions.filter((transaction) => {
            return transaction.type == "C";
          });

          break;
        case "1":
          this.transactions = this.transactions.filter((transaction) => {
            return transaction.type == "D";
          });
          break;
      }
    },
    filterByCategoryFunction() {
      this.transactions = this.transactions.filter((transaction) => {
        if (transaction.category_id == null) {
          return;
        }
        return transaction.category_id.id == this.filterByCategory.id;
      });
    },
    filterByDateFunction() {
      let startDate = new Date(this.filterByDate.startDate);
      let endDate = new Date(this.filterByDate.endDate);

      if (
        (startDate < endDate || startDate.toString() === endDate.toString()) &&
        this.transactions.length != 0
      ) {
        this.transactions = this.transactions.filter((item) => {
          let dateTransaction = new Date(item.date);
          return (
            (dateTransaction > startDate ||
              startDate.toString() == dateTransaction.toString()) &&
            (dateTransaction < endDate ||
              endDate.toString() == dateTransaction.toString())
          );
        });
      } else if (startDate > endDate) {
        this.$toast.error("A data de fim é anterior à data de início");
      }
      return;
    },

    loadPaymentTypes() {
      this.$axios
        .get("/payment-types")
        .then((response) => {
          this.paymentTypes = response.data.data;
          console.log("this.paymentTypes");
          console.log(this.paymentTypes);
        })
        .catch((error) => {
          console.log(error);
        });
    },

    
    loadTransactions() {
      this.$axios
      .get("/vcards/" + this.username + "/transactions")
      .then((response) => {
      this.transactions = response.data.data;
      //Ordena da mais recente para a mais antiga
      this.transactions = this.transactions.sort(
      (a, b) => new Date(b.datetime) - new Date(a.datetime)
      );
      this.$store.commit("setTransaction", this.transactions);
      this.currentTransactions = this.transactions;
      this.paginatedData = this.currentTransactions;
      this.totalTransactions = this.currentTransactions.length;
      this.balance = this.$store.state.vcard.balance;
      })
      .catch(() => {
      this.$toast.error("Sorry!Transactions Temporary Unvaible");
      this.$router.push({ name: "Home" });
      });
    },
    
    changePage(page){
      // update page of items
       this.pagesInfo.pageNumber = page;
    }, 
  },

  mounted() {
    this.loadTransactions();
    this.loadPaymentTypes();

  },
  sockets: {
    newTransactions() {
      this.loadTransactions();
    },
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
