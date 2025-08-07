<template>
  <v-app>
    <!-- ナビゲーションドロワー -->
    <v-navigation-drawer permanent expand-on-hover v-model="drawer" :mini-variant.sync="mini" app
      @mouseenter="showDrawer" @mouseleave="hideDrawer">
      <v-list nav dense>
    
        <!-- ログアウト -->
        <v-list-item @click="logout" dusk="logout">
          <v-list-item-icon class="my-2" :class="{ 'my-1': $vuetify.breakpoint.xs }">
            <v-icon :small="$vuetify.breakpoint.xs">mdi-logout</v-icon>
          </v-list-item-icon>

          <v-list-item-content :class="{ 'py-0': $vuetify.breakpoint.xs }">
            <v-list-item-title>ログアウト</v-list-item-title>
          </v-list-item-content>
        </v-list-item>

      </v-list>
    </v-navigation-drawer>

    <!-- メインコンテンツ -->
    <v-main :style="{ marginLeft: drawer ? '50px' : '-100px' }">
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
  </v-app>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue'

export default {
  components: { Head, Link },

  data: () => ({
    drawer: true,
    mini: true,
    scrollTopBtn: false,
  }),

  methods: {

    // ログアウト
    logout() {
      this.$inertia.post(this.$route('logout'), null, {
        onSuccess: () => {
          this.$toasted.show('ログアウトしました');

          // Sentryのユーザ情報を削除
          this.$sentry.configureScope((scope) => {
            scope.clear()
          });
        }
      })
    },
    onScroll(e) {
      if (typeof window === 'undefined') return;

      const top = window.pageYOffset || e.target.scrollTop || 0;
      this.scrollTopBtn = top > 500;
    },

    showDrawer() {
      this.mini = false;  // ホバー時にドロワーを広げる
    },
    hideDrawer() {
      this.mini = true;  // ホバー外れたらドロワーを小さくする
    }
  }
}
</script>

<style scoped>
/* ナビゲーションドロワーの幅を調整 */
.v-navigation-drawer {
  width: 250px;
  /* 展開時の幅 */
}

.v-navigation-drawer--mini {
  width: 50px;
  /* 最小状態の幅 */
}

/* ドロワーがホバーで展開したときの挙動 */
.v-navigation-drawer:not(.v-navigation-drawer--mini) {
  width: 250px;
}

.v-navigation-drawer--mini {
  width: 50px;
}

/* アプリ全体のレイアウト */
.v-app {
  display: flex;
  height: 100vh;
  /* 画面全体の高さを指定 */
}

/* メインコンテンツをスライドさせる */
.v-main {
  transition: margin-left 0.3s;
  /* スムーズに移動 */
  height: 100vh;
}

.v-container {
  height: 100%;
  /* コンテナ全体の高さを設定 */
}
</style>
