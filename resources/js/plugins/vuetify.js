import Vue from 'vue';
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';

Vue.use(Vuetify);

const opts = {
    theme: {
        themes: {
            light: {
                primary: "#2d9acd",
                // secondary: "#fff",
                // accent: "#fff",
                // error: "#fff",
                // warning: "#fff",
                // info: "#fff",
                // success: "#fff",
                danger: "#ee4444",
            },
        },
    },
};

export default new Vuetify(opts);
