<template>
  <v-btn
      tile depressed
      class="text-capitalize mt-4"
      color="#969797" dark
      @click="back" :small="$vuetify.breakpoint.xs"
  >
    <v-icon left>
      mdi-arrow-left
    </v-icon>
    戻る
  </v-btn>
</template>

<script>
export default {
  props: {
    // 子コンポーネントから戻りページを指定されていれば設定
    backTo: undefined,
  },

  methods: {
    back() {
      const backButton = this.$page['props']['flash']['backButton'];

      // 戻り先ページが指定されていなければブラウザバックを実行
      if (!backButton) {
        window.history.back();
        return;
      }

      // 指定された戻り先ページへ移動
      this.$inertia.get(
          backButton.url,
          undefined,
          {
            onSuccess: page => {
              // ハッシュが指定されていればアンカーリンク相当のスクロールを行う
              if (backButton.hash) {
                this.$vuetify.goTo("#" + backButton.hash);
              }
            },
          }
      )
    }
  },
};
</script>
