import axios from "axios";
import Pusher from "pusher-js";

window.axios = axios;
window.Pusher = Pusher;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
