<template>
  <v-app>

    <Head :title="appName" />

    <!-- メニュー -->
    <v-navigation-drawer v-model="drawer" app dark color="#ffbd66">
      <!-- グローバルナビ -->
      <v-list :class="{ 'pt-8': !$vuetify.breakpoint.xs, 'pt-0': $vuetify.breakpoint.xs }"
        :dense="$vuetify.breakpoint.xs">
        <Link v-for="(item, index) in menuItems" :key="item.title" as="v-list-item" :href="$route(item.to, item.params)"
          exact :class="{ 'v-list-item--active': isActive(item.to, item.params) }"
          :accesskey="(index < 10) ? index : undefined" :dusk="item.dusk">
        <v-list-item-icon class="my-2" :class="{ 'my-1': $vuetify.breakpoint.xs }">
          <v-icon :small="$vuetify.breakpoint.xs">{{ item.icon }}</v-icon>
        </v-list-item-icon>

        <v-list-item-content :class="{ 'py-0': $vuetify.breakpoint.xs }">
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </v-list-item-content>
        </Link>

        <!-- ログアウト -->
        <v-list-item @click="logout" dusk="logout">
          <v-list-item-icon class="my-2" :class="{ 'my-1': $vuetify.breakpoint.xs }">
            <v-icon :small="$vuetify.breakpoint.xs">mdi-logout</v-icon>
          </v-list-item-icon>

          <v-list-item-content :class="{ 'py-0': $vuetify.breakpoint.xs }">
            <v-list-item-title>戻る</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar app color="#1c6181" dark flat>
      <Link as="div" style="cursor: pointer;font-size: larger;" :href="$route('home')">
      GROUPTUBE {{ type }}
      </Link>


      <!-- <Link as="div" style="cursor: pointer" :href="$route('home')">
          <v-app-bar-title class="py-0 px-4">
        
      
          </v-app-bar-title>
        </Link> -->

      <v-spacer></v-spacer>

      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
    </v-app-bar>

    <v-main>
      <v-container :class="{ 'pa-0': $vuetify.breakpoint.xs }" style="margin: initial;" fluid>
        <slot />
      </v-container>

      <!-- ページトップスクロールボタン -->
      <transition name="scroll-top-btn">
        <v-btn fab dark fixed bottom left class="mb-n2" color="info" v-scroll="onScroll" v-show="scrollTopBtn"
          @click="$vuetify.goTo(0)">
          <v-icon large>mdi-arrow-up-bold</v-icon>
        </v-btn>
      </transition>
    </v-main>

    <v-footer app dark absolute inset color="#1c6181">
      <v-spacer></v-spacer>
      <div class="text-caption">
        Copyright (C) Cab Station Co., Ltd. All rights reserved.
      </div>
      <v-spacer></v-spacer>
    </v-footer>
  </v-app>
</template>
  
<style scoped>
/* ページトップスクロールボタンアニメーション */
.scroll-top-btn-enter-active,
.scroll-top-btn-leave-active {
  transition: 0.5s;
}

.scroll-top-btn-enter,
.scroll-top-btn-leave-to {
  opacity: 0;
  transform: scale(0);
}

.v-list .v-list-item {
  min-height: 0px !important;
}
</style>
  
<script>
import { Head, Link } from '@inertiajs/inertia-vue'

export default {
  components: { Head, Link },

  props:['type_list'],

  data: () => ({
    drawer: null,
    menuItems: [

      { title: 'ICTS', to: 'sales.index', params: { type: 'icts'}, icon: 'mdi-file-document-edit', dusk: "ictsIndex" },
      { title: 'TCD', to: 'sales.index', params: { type: 'tcd'}, icon: 'mdi-file-document-edit', dusk: "tcdIndex" },
      { title: 'BPD', to: 'sales.index', params: { type: 'bpd' }, icon: 'mdi-file-document-edit', dusk: "bpdIndex" },
      { title: 'WSD', to: 'sales.index', params: { type: 'wsd'}, icon: 'mdi-file-document-edit', dusk: "wsdIndex" },

    ],
    scrollTopBtn: false
  }),

  computed: {
    appName() {
      return this.$page.props.appName
    },
  },

  mounted() {
    // ハッシュ値が設定されていればスムーズスクロールを行う
    if (location.hash) {
      const hash = location.hash.substr(1)
      if (document.getElementById(hash)) {
        this.$vuetify.goTo("#" + hash);
      }
    }
  },

  methods: {
    // アクティブリンク判定
    isActive($to, $param) {
      if ($to === "home" && this.$page.component.startsWith('Home')) {
        return true;
      } else if ($to === "sales-todos.index" && this.$page.component.startsWith('SalesTodos')) {
        return true;
      } else if ($to === "office-todos.index" && this.$page.component.startsWith('OfficeTodos')) {
        return true;
      } else if ($to === "client-types.clients.index" && this.$page.url.startsWith('/client-types/' + $param.client_type)) {
        return true;
      } else if ($to === "client-types.clients.index" && this.$page.props.client?.client_type_id === $param.client_type) {
        return true
      } else if ($to === "client-types.clients.index" && this.$page.props.branch?.client.client_type_id === $param.client_type) {
        return true;
      } else if ($to === "client-types.clients.index" && $param.client_type === "taxibus" && this.$page.component.startsWith('Vehicles')) {
        return true;
      } else if ($to === "product-evaluations" && this.$page.component.startsWith('ProductEvaluations')) {
        return true;
      } else if ($to === "reports.index" && this.$page.component.startsWith('Reports')) {
        return true;
      } else if ($to === "meetings.index" && this.$page.component.startsWith('Meetings')) {
        return true;
      } else if ($to === "notices.index" && this.$page.component.startsWith('Notices')) {
        return true;
      } else if ($to === "users.index" && this.$page.component.startsWith('User')) {
        return true;
      } else if ($to === "documents" && this.$page.component.startsWith('Documents')) {
        return true;
      } else {
        return false;
      }
    },

    // ログアウト
    logout() {
      this.$inertia.get('/');
      // this.$inertia.post(this.$route('logout'), null, {
      //   onSuccess: () => {
      //     this.$toasted.show('ログアウトしました');

      //     // Sentryのユーザ情報を削除
      //     this.$sentry.configureScope((scope) => {
      //       scope.clear()
      //     });
      //   }
      // })
    },

    // ページトップスクロールボタン
    onScroll(e) {
      if (typeof window === 'undefined') return;

      const top = window.pageYOffset || e.target.scrollTop || 0;

      this.scrollTopBtn = top > 500;
    }
  }
}
</script>