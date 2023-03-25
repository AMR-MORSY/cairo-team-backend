import axios from "axios";
import router from "../router/index";
import store from "../vuex/store";


let Api = axios.create({
  
    headers: {
        "Content-Type": "application/json",
      
       
        
    },
    
});

Api.defaults.withCredentials = true;
Api.defaults.baseURL= import.meta.env.VITE_BASE_URL;

Api.interceptors.response.use(
    function (response) {
        return response;
    },
    function (error) {
        if (
            error.response.status == 403 ||
            error.response.status == 419 ||
            error.response.status == 401
        ) {
            // sessionStorage.removeItem("Auth");
            sessionStorage.removeItem("User");
            // store.dispatch("changeLoginState", false);
            store.dispatch("userData", null);
            // store.dispatch("userPermissions", null);
            // store.dispatch("userRoles", null);
            router.push({ path: "/user/login" });
        }

        return Promise.reject(error);
    }
);

let downloadApi = axios.create({
 
    responseType: "blob",
    headers: {
        "Content-Type": "application/json",
    },
   
});
let uploadApi=axios.create({
 
    headers: {
        "Content-Type": "multipart/form-data",
    },

});
uploadApi.defaults.withCredentials=true;
uploadApi.defaults.baseURL= import.meta.env.VITE_BASE_URL;
uploadApi.interceptors.response.use(
    function (response) {
        return response;
    },
    function (error) {
        if (
            error.response.status == 403 ||
            error.response.status == 419 ||
            error.response.status == 401
        ) {
            sessionStorage.removeItem("Auth");
            sessionStorage.removeItem("userData");
            store.dispatch("changeLoginState", false);
            store.dispatch("userData", null);
            store.dispatch("userPermissions", null);
            store.dispatch("userRoles", null);
            router.push({ path: "/user/login" });
        }

        return Promise.reject(error);
    }

);
downloadApi.defaults.withCredentials = true;
downloadApi.defaults.baseURL= import.meta.env.VITE_BASE_URL;
downloadApi.interceptors.response.use(
    function (response) {
        return response;
    },
    function (error) {
        if (
            error.response.status == 403 ||
            error.response.status == 419 ||
            error.response.status == 401
        ) {
            sessionStorage.removeItem("Auth");
            sessionStorage.removeItem("userData");
            store.dispatch("changeLoginState", false);
            store.dispatch("userData", null);
            store.dispatch("userPermissions", null);
            store.dispatch("userRoles", null);
            router.push({ path: "/user/login" });
        }

        return Promise.reject(error);
    }
);

function getUserToken()
{
    let user = sessionStorage.getItem("User");
    console.log(user)
    if(user)
    {
        let userArray = JSON.parse(user);
        let userToken = userArray.token.plainTextToken;
        console.log(userToken)
       
         return userToken;

    }
   else
   {
    return null;
   }

}
function allInstances() {
    return {
        Api: Api,
        downloadApi: downloadApi,
        uploadApi:uploadApi,
    };
}

export default allInstances();
