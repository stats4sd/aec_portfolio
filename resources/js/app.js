import axios from 'axios';
import _ from 'lodash';
import Swal from 'sweetalert2'


window._ = _;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.swal = Swal;
