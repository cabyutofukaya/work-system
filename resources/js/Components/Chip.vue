<template>
  <v-chip
      small class="mr-2 mb-2"
      filter :input-value="true"
      :filter-icon="Boolean(value) ? 'mdi-check' : 'mdi-close'"
      :color="Boolean(value) ? 'primary' : 'default'"
      :disabled="!Boolean(value)"
  >
    <slot></slot>
  </v-chip>
</template>

<script>
export default {
  props: {
    value: true,
  },
};
</script>