<template>
  <v-btn
      tile depressed
      class="text-capitalize"
      :color="color"
      :dark="!disabled"
      :disabled="disabled"
      v-bind="$attrs"
      @click="visit"
  >
    <slot></slot>
  </v-btn>
</template>

<script>
export default {
  props: {
    // to属性が設定された場合はinertia visitを実行
    // Linkコンポーネントのas属性のカスタムコンポーネントを指定するとhref属性による移動が動作しないため
    to: {
      type: String,
      default: "",
    },
    color: {
      type: String,
      default: "#969797",
    },
    // disabled属性を設定
    // trueに設定するとボタン場非表示となるため同時にdark属性をfalseに設定する
    // c.f. [Vuetifyのボタンにdisabledを設定するとボタンが非表示になる時の対処法](https://zenn.dev/kigi/scraps/922e5fbd6bb263)
    disabled: {
      type: Boolean,
      default: false,
    },
  },

  methods: {
    // inertia visitを実行
    visit() {
      if (this.to) {
        this.$inertia.get(this.to);
      }
    }
  }
};
</script>