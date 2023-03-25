import allInstances from "./Api";

export default {
  register(form) {
    return allInstances.Api.post("/user/register", form);
  },

  login(form) {
    return allInstances.Api.post("/user/login", form);
  },

  resetPassword(form) {
    return allInstances.Api.post("/user/resetPassword", form);
  },
  validateToken(form) {
    return allInstances.Api.post("/user/validateToken", form);
  },
  sendToken(form) {
    return allInstances.Api.post("/user/sendToken", form);
  },
  refreshSession() {
    return allInstances.Api.get("/user/refreshsession");
  },
};
