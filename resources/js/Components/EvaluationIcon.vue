<template>
  <v-icon
      v-text="'mdi-alpha-' + evaluationLowerCase + '-circle'"
      :color="color"
  >
  </v-icon>
</template>

<script>
export default {
  props: {
    evaluation: undefined,
  },

  computed: {
    evaluationLowerCase: function () {
      return this.evaluation.toLowerCase();
    },

    color: function () {
      switch (this.evaluationLowerCase) {
        case 's':
          return 'green';
        case 'a':
          return 'light-green';
        case 'b':
          return 'orange'
        case 'c':
          return 'red'
        case 'd':
          return 'purple'
        case 'e':
          return 'gray'
      }

      return null;
    }
  },
};
</script>
