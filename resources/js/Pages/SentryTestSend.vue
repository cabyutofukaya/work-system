<template>
  <div>
    send sentry error tracking test
  </div>
</template>

<script>
export default {
  mounted() {
    this.$sentry.captureMessage("error tracking test");
  }
}
</script>

