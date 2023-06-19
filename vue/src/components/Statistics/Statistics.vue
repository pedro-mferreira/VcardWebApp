<template>
  <div v-if="show==true">
    <div class="d-flex justify-content-between">
      <div class="mx-2">
        <h3 class="mt-4">Statistics</h3>
      </div>
    </div>
    <div class="d-flex justify-content-around">
      <div>
        <h3 style="text-align: center">Categories Used</h3>
        <categories-chart
          v-if="state.isLoaded"
          v-bind:chartData="state.chartData"
          v-bind:chartOptions="state.chartOptions"
        />
      </div>
      <div>
        <h3 style="text-align: center">Payment Types Used</h3>
        <payment-type-chart
          v-if="statePaymentType.isLoaded"
          v-bind:chartData="statePaymentType.chartData"
          v-bind:chartOptions="statePaymentType.chartOptions"
        />
      </div>
    </div>

    <div class="container" v-if="this.$store.state.user.user_type == 'V'">
      <h3 style="text-align: center">Debit / Credit</h3>
      <div class="row">
        <div class="col-2">
          </div>
        <div class="col-2">
          <div > 
            <div class="row" style="margin-bottom:20px">
              <small>StartDate:</small>
              <input id="dateStart" type="date" v-model="startDate" />
            </div>
            <div class="row" style="margin-bottom:20px">
              <small>EndDate:</small>
              <input id="dateEnd" type="date" v-model="endDate" />
            </div>
            <div class="row">
              <button
                type="button"
                class="btn btn-success px-4 btn-addtask"
                @click="filterByDate"
              >
                Filter
              </button>
            </div>
          </div>
        </div>
        <div class="col-2">
          </div>
        <div class="col-4">
          <debit-credit-chart
            v-if="stateDebitCredit.isLoaded"
            v-bind:chartData="stateDebitCredit.chartData"
            v-bind:chartOptions="stateDebitCredit.chartOptions"
          />
        </div>
      </div>
    </div>
    <div></div>
  </div>
  <div v-if="show==false">
    <h3 style="text-align:center; color:red">Sem transações</h3>
  </div>
</template>


<script>
import CategoriesChart from "./CategoriesChart.vue";
import PaymentTypeChart from "./PaymentTypeChart.vue";
import DebitCreditChart from "./DebitCreditChart.vue";
export default {
  components: {
    CategoriesChart,
    PaymentTypeChart,
    DebitCreditChart,
  },

  data() {
    return {
      state: {
        isLoaded: false,
        chartData: {},
        chartOptions: {
          responsive: true,
        },
      },

      statePaymentType: {
        isLoaded: false,
        chartData: {},
        chartOptions: {
          responsive: true,
        },
      },

      stateDebitCredit: {
        isLoaded: false,
        chartData: {},
        chartOptions: {
          responsive: true,
        },
      },

      transactions: [],
      filteredTransactions: [],

      startDate: null,
      endDate: null,
      show:false
    };
  },
  mounted() {
    this.state.isLoaded = false;
    this.statePaymentType.isLoaded = false;
    this.stateDebitCredit.isLoaded = false;

    let arr = [];
    if (this.$store.state.user.user_type == "V") {
      console.log("ENTROU NO DO VCARD");
      this.$axios
        .get("vcards/" + this.$store.state.user.username + "/transactions")
        .then((response) => {
          arr = response.data.data;
          this.transactions = arr;
          if(this.transactions.length != 0){
            this.show = true;
          }
          this.filteredTransactions = this.transactions;
          const counts = {};
          arr.forEach((element) => {
            if (element.category_id != null) {
              counts[element.category_id.name] = counts[
                element.category_id.name
              ]
                ? counts[element.category_id.name] + 1
                : 1;
            }
          });

          var rgb = [];

          for (var i = 0; i < Object.keys(counts).length; i++) {
            var color = Math.floor(Math.random() * 16777215).toString(16);
            rgb.push("#" + color);
          }

          this.state.chartData = {
            labels: Object.entries(counts),
            datasets: [
              {
                backgroundColor: rgb,
                data: Object.values(counts),
              },
            ],
          };

          const countsPaymentTypes = {};
          arr.forEach((element) => {
            if (element.payment_type != null) {
              countsPaymentTypes[element.payment_type.name] =
                countsPaymentTypes[element.payment_type.name]
                  ? countsPaymentTypes[element.payment_type.name] + 1
                  : 1;
            }
          });

          var rgbPT = [];

          for (var j = 0; j < Object.keys(countsPaymentTypes).length; j++) {
            var color2 = Math.floor(Math.random() * 16777215).toString(16);
            rgbPT.push("#" + color2);
          }

          this.statePaymentType.chartData = {
            labels: Object.entries(countsPaymentTypes),
            datasets: [
              {
                backgroundColor: rgbPT,
                data: Object.values(countsPaymentTypes),
              },
            ],
          };

          this.state.isLoaded = true;
          this.statePaymentType.isLoaded = true;
          console.log(this.$refs);
        });
    }

    if (this.$store.state.user.user_type == "A") {
      this.show = true;
      console.log("ENTROU NO DO ADMIN");
      this.$axios.get("/transactionsStatistics").then((response) => {
        console.log("DATA", response.data);
        console.log(
          "PAYMENT",
          response.data[0].map((a) => a.payment_type).length
        );

        var rgb = [];

        for (
          var i = 0;
          i < response.data[0].map((a) => a.payment_type).length;
          i++
        ) {
          var color = Math.floor(Math.random() * 16777215).toString(16);
          rgb.push("#" + color);
        }

        var rgbPT = [];

        for (var j = 0; j < response.data[1].map((a) => a.name).length; j++) {
          var color2 = Math.floor(Math.random() * 16777215).toString(16);
          rgbPT.push("#" + color2);
        }

        this.state.chartData = {
          labels: response.data[1].map((a) => a.name),
          datasets: [
            {
              backgroundColor: rgbPT,
              data: response.data[1].map((a) => a.total),
            },
          ],
        };

        this.statePaymentType.chartData = {
          labels: response.data[0].map((a) => a.payment_type),
          datasets: [
            {
              backgroundColor: rgb,
              data: response.data[0].map((a) => a.total),
            },
          ],
        };

        this.state.isLoaded = true;
        this.statePaymentType.isLoaded = true;
      });
    }
  },
  methods: {
    filterByDate() {
      this.stateDebitCredit.isLoaded = false;
      if (this.startDate != "" && this.endDate != "") {
        let startDatefunction = new Date(this.startDate);
        let endDatefunction = new Date(this.endDate);

        if (
          (startDatefunction < endDatefunction ||
            startDatefunction.toString() === endDatefunction.toString()) &&
          this.transactions.length != 0
        ) {
          this.filteredTransactions = this.transactions.filter((item) => {
            let dateTransaction = new Date(item.date);
            return (
              (dateTransaction > startDatefunction ||
                startDatefunction.toString() == dateTransaction.toString()) &&
              (dateTransaction < endDatefunction ||
                endDatefunction.toString() == dateTransaction.toString())
            );
          });
          this.doGraphic();
        } else if (startDatefunction > endDatefunction) {
          this.$toast.error("A data de fim é anterior à data de início");
        }
      } else if (this.startDate != "" || this.endDate != "") {
        this.$toast.error("StartDate and EndDate  should be filled!");
      }
    },
    doGraphic() {
      var valores = [];
      valores["debit"] = 0;
      valores["credit"] = 0;
      this.filteredTransactions.forEach((element) => {
        if (element.type == "D") {
          valores["debit"] += parseFloat(element.value);
        }
        if (element.type == "C") {
          valores["credit"] += parseFloat(element.value);
        }
      });
      this.stateDebitCredit.chartData = {
        labels: ["Debit", "Credit"],
        datasets: [
          {
            backgroundColor: ["red", "green"],
            data: [valores["debit"], valores["credit"]],
          },
        ],
      };

      this.stateDebitCredit.isLoaded = true;
      console.log(valores);
    },
  },
};
</script>