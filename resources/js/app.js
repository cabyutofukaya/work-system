require('./bootstrap');

import Vue from 'vue'

// Vue.js devtoolsブラウザ拡張によるデバッグを有効化
Vue.config.devtools = true;

// アプリ設定をmetaタグから取得
Vue.prototype.$config = JSON.parse(document.head.querySelector('meta[name="app-config"]').content);

// ほかモジュールより先にSentryをインポート
import * as Sentry from "@sentry/vue";
Vue.prototype.$sentry = Sentry; // グローバルに設定
import {BrowserTracing} from "@sentry/tracing";

// 環境変数にdnsがあればSentryを設定
if (Vue.prototype.$config['sentry_dsn']) {
    Vue.prototype.$sentry.init({
        Vue,
        dsn: Vue.prototype.$config['sentry_dsn'],
        integrations: [new BrowserTracing()],
        tracesSampleRate: 1.0, // We recommend adjusting this value in production, or using tracesSampler for finer control
        logErrors: true,
    });
}

import {createInertiaApp} from '@inertiajs/inertia-vue'
import {InertiaProgress} from '@inertiajs/progress';
import vuetify from './plugins/vuetify'
import Toasted from 'vue-toasted';
import VuetifyConfirm from 'vuetify-confirm';
import VueCurrencyFilter from 'vue-currency-filter';
import * as GmapVue from 'gmap-vue';

// Ziggyによるrouteヘルパをグローバルに利用可能にする
Vue.prototype.$route = route;

// vuetify-confirm
Vue.use(VuetifyConfirm, {
    vuetify,
    buttonTrueText: 'OK',
    buttonFalseText: 'キャンセル',
    color: 'error',
    icon: 'mdi-alert',
    title: '確認'
});

// vue-toasted
Vue.use(Toasted, {
    position: 'bottom-center',
    duration: 2000,
    type: 'success',
});

// vue-currency-filter
Vue.use(VueCurrencyFilter, {
    symbol: '￥',
    thousandsSeparator: ',',
    symbolSpacing: false,
});

// GmapVue
Vue.use(GmapVue, {
    load: {
        key: Vue.prototype.$config['google_map_api_key'],
        libraries: 'places',
        region: 'JP',
        language: 'ja'
    }
});

// Vue.prototype.$browserBackFlg = false
// history.replaceState(null, '', null)
// window.addEventListener('popstate', function() {
//   Vue.prototype.$browserBackFlg = true

//   window.setTimeout(() => {
//     Vue.prototype.$browserBackFlg = false
//   }, 500)
// })

// グローバルコンポーネントを登録
import Button from './Components/Button';
import BackButton from './Components/BackButton';
import Chip from './Components/Chip';
import EvaluationIcon from './Components/EvaluationIcon';
import GmapViewer from './Components/GmapViewer';
import GmapPicker from './Components/GmapPicker';
import ImageOverlay from './Components/ImageOverlay';
import ProductEvaluationChart from './Components/ProductEvaluationChart';

Vue.component('Button', Button);
Vue.component('BackButton', BackButton);
Vue.component('Chip', Chip);
Vue.component('EvaluationIcon', EvaluationIcon);
Vue.component('GmapViewer', GmapViewer);
Vue.component('GmapPicker', GmapPicker);
Vue.component('ImageOverlay', ImageOverlay);
Vue.component('ProductEvaluationChart', ProductEvaluationChart);

createInertiaApp({
    resolve: name => require(`./Pages/${name}`),
    setup({el, App, props, plugin}) {
        Vue.use(plugin);

        new Vue({
            vuetify,
            render: h => h(App, props),
        }).$mount(el)
    },
});

InertiaProgress.init({color: 'yellow'});