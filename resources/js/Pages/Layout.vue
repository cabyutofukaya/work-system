<template>
  <v-app>
    <!-- ナビゲーションドロワー -->
    <v-navigation-drawer permanent expand-on-hover v-model="drawer" :mini-variant.sync="mini" app
      @mouseenter="showDrawer" @mouseleave="hideDrawer">
      <v-list nav dense>
        <v-list-item v-for="(item, index) in menuItems" :key="index" link>
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>
          <v-list-item-title :style="{ fontWeight: 'bold' }">{{ item.title }}</v-list-item-title>
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
    menuItems: [
      { title: 'ホーム', to: 'home', icon: 'mdi-view-dashboard' },
      // { title: '議事録', to: 'meetings.index', icon: 'mdi-file-document-edit' },
      // { title: '掲示板', to: 'boardings', icon: 'mdi-file-document-edit' },
      // { title: 'お知らせ', to: 'notices.index', icon: 'mdi-information' },
      // { title: '会議室予約', to: 'bookings.index', icon: 'mdi-table-edit' },
      // { title: '書類情報', to: 'documents', icon: 'mdi-file-download' },
      // { title: 'ユーザー管理', to: 'users.index', icon: 'mdi-account' },
      // { title: 'スケジュール', to: 'schedule.index', icon: 'mdi-calendar' },
      // { title: 'ToDo', to: 'office-todos.index', icon: 'mdi-calendar' },
    ]
  }),

  methods: {
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
