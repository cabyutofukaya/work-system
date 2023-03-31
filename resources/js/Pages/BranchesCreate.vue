<template>
  <Layout>
    <div>
      <v-card
          flat tile
      >
        <v-card-title>
          <v-icon dark left>mdi-office-building</v-icon>
          営業所の新規作成
        </v-card-title>

        <form @submit.prevent="create">
          <v-card-text>
            <div class="description-form">
              <v-row>
                <v-col cols="12" sm="4">営業所名</v-col>
                <v-col>
                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      name="name"
                      v-model="form.name"
                      maxlength="200"
                      :error="Boolean(form.errors.name)"
                      :error-messages="form.errors.name"
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">郵便番号</v-col>
                <v-col>
                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      hint="半角数字のみ7桁で入力してください"
                      persistent-hint
                      name="postcode"
                      v-model="form.postcode"
                      maxlength="200"
                      :error="Boolean(form.errors.postcode)"
                      :error-messages="form.errors.postcode"
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">所在地</v-col>
                <v-col>
                  <Button
                      class="mb-2 mr-2" :small="$vuetify.breakpoint.xs"
                      @click.native="getAddressFromPostcode"
                  >
                    郵便番号から所在地を取得する
                  </Button>

                  <v-select
                      dense filled
                      label="都道府県"
                      :items="prefectures"
                      name="prefecture"
                      v-model="form.prefecture"
                      :error="Boolean(form.errors.prefecture)"
                      :error-messages="form.errors.prefecture"
                  >
                  </v-select>

                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      label="市区町村以下"
                      name="address"
                      v-model="form.address"
                      maxlength="200"
                      :error="Boolean(form.errors.address)"
                      :error-messages="form.errors.address"
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">位置情報</v-col>
                <v-col>
                  <input type="hidden" v-model="form.lat">
                  <input type="hidden" v-model="form.lng">

                  <!-- ジオコーディングAPIにより住所から位置情報を取得する -->
                  <Link as="Button" class="mb-2 mr-2" @click.native="getLatLngFromAddress">所在地から位置情報を取得する</Link>

                  <!-- 位置情報ピッカーコンポーネントをひらく -->
                  <Link as="Button" class="mb-2 mr-2" @click.native="openGmapPicker = true">位置情報を手動で修正する</Link>

                  <!-- 位置情報マップ表示コンポーネント -->
                  <GmapViewer
                      :lat="form.lat"
                      :lng="form.lng"
                  >
                  </GmapViewer>

                  <!-- フルスクリーン位置情報ピッカーコンポーネント -->
                  <GmapPicker
                      v-if="openGmapPicker"
                      :openGmapPicker="openGmapPicker"
                      :lat="form.lat"
                      :lng="form.lng"
                      v-on:closeGmapPicker="closeGmapPicker"
                      v-on:getGmapPickerCoordinates="getGmapPickerCoordinates"
                  >
                  </GmapPicker>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">電話番号</v-col>
                <v-col>
                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      hint="半角数字とハイフンのみで入力してください" persistent-hint
                      name="tel"
                      v-model="form.tel"
                      maxlength="200"
                      :error="Boolean(form.errors.tel)"
                      :error-messages="form.errors.tel"
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">FAX番号</v-col>
                <v-col>
                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      hint="半角数字とハイフンのみで入力してください" persistent-hint
                      name="fax"
                      v-model="form.fax"
                      maxlength="200"
                      :error="Boolean(form.errors.fax)"
                      :error-messages="form.errors.fax"
                  ></v-text-field>
                </v-col>
              </v-row>
            </div>

            <v-col class="text-right">
              <Button
                  color="primary"
                  :small="$vuetify.breakpoint.xs"
                  type="submit"
                  :loading="loading['create']"
              >
                <v-icon left>
                  mdi-content-save-outline
                </v-icon>
                この内容で営業所を作成する
              </Button>
            </v-col>
          </v-card-text>
        </form>

        <v-divider></v-divider>

        <v-card-text class="text-right">
          <BackButton></BackButton>
        </v-card-text>
      </v-card>

      <!-- Google Map APIを直接使用するため不可視のGmapMapコンポーネントを呼び出す -->
      <GmapMap :center="{lat:form.lat, lng:form.lng}"></GmapMap>
    </div>
  </Layout>
</template>

<script>
import Layout from "./Layout";
import {Link} from "@inertiajs/inertia-vue";
import {gmapApi} from "gmap-vue";

export default {
  components: {Layout, Link},

  props: ['prefectures', 'client', 'location_default'],

  data() {
    return {
      // フォームヘルパ
      form: this.$inertia.form({
        id: null,
        client_id: this.client.id,
        name: null,
        postcode: null,
        prefecture: null,
        address: null,
        // 現在位置の初期値
        lat: this.location_default["lat"],
        lng: this.location_default["lng"],
        tel: null,
        fax: null,
      }),
      openGmapPicker: false,
      loading: {}
    }
  },

  computed: {
    google: gmapApi, // gmap-vueでAPIへの直接のアクセスを可能にする
  },

  methods: {
    // YubinBango.jsによる住所自動入力
    getAddressFromPostcode() {
      let _this = this;
      new YubinBango.Core(_this.form.postcode, function (addr) {
        _this.form.prefecture = addr.region; // 値が都道府県名の場合
        //_this.form.prefecture = addr.region_id; // 値が都道府県番号の場合
        _this.form.address = addr.locality + addr.street;
      })
    },

    // ジオコーディングAPIにより住所から位置情報を取得する
    getLatLngFromAddress() {
      const address = this.form.prefecture + this.form.address;
      if (this.google && address) {
        new this.google.maps.Geocoder().geocode(
            {address: address},
            (results, status) => {
              if (status === this.google.maps.GeocoderStatus.OK) {
                const location = results[0].geometry.location;
                this.form.lat = location.lat();
                this.form.lng = location.lng();
              } else {
                this.$toasted.error('所在地から位置情報を設定できませんでした');
              }
            }
        );
      } else {
        this.$toasted.error('所在地が設定されていません');
      }
    },

    // 子コンポーネントから修正した位置情報を受け取る
    getGmapPickerCoordinates: function (data) {
      this.form.lat = data.lat;
      this.form.lng = data.lng;
    },

    // 子コンポーネントから地図ダイアログを閉じるイベントを受け取る
    closeGmapPicker: function () {
      this.openGmapPicker = false;
    },

    // 営業所情報作成
    create: function () {
      this.form.post(this.$route('clients.branches.store', {client: this.client.id}), {
        onStart: () => this.$set(this.loading, "create", true),
        onSuccess: () => {
          this.$toasted.show('営業所を作成しました');

          // id="branches"へのスクロール
          this.$vuetify.goTo("#branches");
        },
        onFinish: () => this.$set(this.loading, "create", false),
      })
    }
  }
}
</script>
