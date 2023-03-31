<template>
  <Layout>
    <div>
      <v-card
          flat tile
      >
        <v-card-title>
          <v-icon dark left>mdi-car</v-icon>
          保有車両 {{ vehicle['type_name'] }}
        </v-card-title>

        <v-card-text>
          <v-row class="mt-0">
            <v-col class="text-right">
              <Link
                  as="Button"
                  :small="$vuetify.breakpoint.xs"
                  :to="$route('vehicles.edit',{vehicle : vehicle['id']})"
              >
                <v-icon left>
                  mdi-pencil
                </v-icon>
                この車両情報を編集する
              </Link>
            </v-col>
          </v-row>
        </v-card-text>

        <v-card-text>
          <div class="description-list">
            <v-row class="mt-0">
              <v-col cols="12" sm="4">車両画像</v-col>
              <v-col>
                <a class="d-inline-block" @click.prevent="imageOverlayUrl = vehicle['image_url']">
                  <v-img
                      :max-width="$vuetify.breakpoint.xs ? '80vw' : '300px'"
                      :src="vehicle['image_url']" alt=""
                  ></v-img>
                </a>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">車両名</v-col>
              <v-col>
                {{ vehicle["name"] }}
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">車両説明</v-col>
              <v-col>
                <span style="white-space: pre-line;">{{ vehicle["description"] }}</span>
              </v-col>
            </v-row>
          </div>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-text class="text-right">
          <BackButton></BackButton>
        </v-card-text>
      </v-card>
    </div>

    <!-- イメージオーバーレイ表示 -->
    <ImageOverlay v-bind:url.sync="imageOverlayUrl"></ImageOverlay>
  </Layout>
</template>

<script>
import Layout from "./Layout";
import {Link} from "@inertiajs/inertia-vue";

export default {
  components: {Layout, Link},

  props: ['vehicle'],

  data: () => ({
    imageOverlayUrl: undefined
  }),
}
</script>
