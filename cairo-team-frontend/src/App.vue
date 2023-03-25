<template >
  <navbar @displaySitesTable="displaySitesTable"></navbar>
  <router-view></router-view>

  <DynamicDialog  />
  <ConfirmDialog ></ConfirmDialog>

  <SpinnerPage :displayNone="displaySpinnerPage"></SpinnerPage>
  <!-- <DialogMessage :displayDialog="displayDialog" :dialogMessage="dialogMessage"/> -->
</template>

<script >
import User from "./apis/User";


// import DialogMessage from "./DialogMessage.vue";
import SpinnerPage from "../src/components/helpers/SpinnerPage.vue";
import navbar from "../src/components/navbar.vue";
import SitesTable from "../src/components/pages/sites/SitesTable.vue";

export default {
  components: { SpinnerPage, navbar,SitesTable },
  data() {
    return {
      showModal: false,

      data: "session will end after 2 minutes, renew session",
    };
  },
 

  computed: {
    displaySpinnerPage() {
      return this.$store.state.displaySpinnerPage;
    },
    displayDialog() {
      return this.$store.state.displayDialog;
    },
    dialogMessage() {
      return this.$store.state.dialogMessage;
    },

    // sessoinTimeOut() {
    //   if (this.$store.state.sessionTimeOut) {
    //     return true;
    //   } else {
    //     return false;
    //   }
    // },
    // sessionEnd() {
    //   if (this.$store.state.sessionEnd) {
    //     return true;
    //   }
    // },
  },
  name: "app",

  methods: {
    // showSessionTimeOutNotification() {
    //   this.$confirm.require({
    //     message: "Session will expire in 2 minutes.renew the session?",
    //     header: "Confirmation",
    //     icon: "pi pi-exclamation-triangle",
    //     position: "top",
    //     acceptClass: "p-button-help",
    //     rejectClass: "p-button-danger",
    //     accept: () => {
    //       this.$confirm.close();
    //       this.refreshSession();
    //     },
    //     reject: () => {
    //       this.$confirm.close();
    //       this.goToLogin();
    //     },
    //   });
    // },
    displaySitesTable(event) {
      this.$dialog.open(SitesTable, {
        props: {
          header: "Sites",
          style: {
            width: "75vw",
          },
          breakpoints: {
            "960px": "75vw",
            "640px": "90vw",
          },
          modal: true,
        },

        data: {
          sites: event,
        },
      });
    },
    displayTheSpinner(event) {
      this.displaySpinnerPage = event;
    },
    // refreshSession() {
    //   User.refreshSession()
    //     .then((response) => {
    //       sessionStorage.setItem("Auth", true);
    //       sessionStorage.setItem("userData", JSON.stringify(response.data));
    //       this.$store.dispatch("changeLoginState", true);
    //       this.$store.dispatch("userData", response.data.user);
    //       this.$store.dispatch("userPermissions", response.data.permissions);
    //       this.$store.dispatch("userRoles", response.data.roles);
    //     })
    //     .catch((error) => {
    //       if (error) {
    //         sessionStorage.removeItem("Auth");
    //         sessionStorage.removeItem("userData");
    //         this.$store.dispatch("changeLoginState", false);
    //         this.$store.dispatch("userData", null);
    //         this.$store.dispatch("userPermissions", null);
    //         this.$store.dispatch("userRoles", null);
    //         this.$router.push({ path: "/user/login" });
    //       }
    //     });
    //   // this.$store.dispatch("changeTimeOut",false);
    // },
    // goToLogin() {
    //   sessionStorage.removeItem("Auth");
    //   sessionStorage.removeItem("userData");
    //   this.$store.dispatch("changeLoginState", false);
    //   this.$store.dispatch("userData", null);
    //   this.$store.dispatch("userPermissions", null);
    //   this.$store.dispatch("userRoles", null);
    //   this.$router.push({ path: "/user/login" });
    // },
  },

  
};
</script>

<style lang="scss" >
body {
  background-color: #dde0e3;
}
</style>