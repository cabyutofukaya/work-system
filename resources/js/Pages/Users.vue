<template>
  <Layout>
    <v-card flat tile>
      <v-card-title>
        <v-icon dark left>mdi-account</v-icon>
        メンバー
      </v-card-title>

      <v-card-text>

        <v-row v-if="windowSize >= 800">

          <v-col>
            <Link as="Button" :small="$vuetify.breakpoint.xs" @click.native="searchDialog = true"
              :color="formParamsCount ? 'warning' : undefined">
            <v-icon left>
              mdi-filter-outline
            </v-icon>
            絞り込み
            <!-- <span v-if="formParamsCount">({{ formParamsCount }})</span> -->
            </Link>
          </v-col>

          <v-col class="text-right">
            <Button :small="$vuetify.breakpoint.xs" :to="$route('user-profile-information.edit')">
              <v-icon left>
                mdi-pencil
              </v-icon>
              自分のメンバー情報を編集する
            </Button>
          </v-col>
        </v-row>

        <v-row v-if="windowSize < 800">

          <v-col class="text-right">

            <Link as="Button" :small="$vuetify.breakpoint.xs" @click.native="searchDialog = true"
              :color="formParamsCount ? 'warning' : undefined">
            <v-icon left>
              mdi-filter-outline
            </v-icon>
            絞り込み
            <!-- <span v-if="formParamsCount">({{ formParamsCount }})</span> -->
            </Link>
            
            <Button :small="$vuetify.breakpoint.xs" :to="$route('user-profile-information.edit')">
              <v-icon left>
                mdi-pencil
              </v-icon>
              自分のメンバー情報を編集する
            </Button>
          </v-col>
        </v-row>

      </v-card-text>


      <!-- 絞り込み条件ダイアログ -->
      <v-dialog v-model="searchDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'">
        <v-card flat tile>
          <v-card-title>
            絞り込み
          </v-card-title>

          <form @submit.prevent="search">
            <v-card-text>
              <v-list>

                <v-list-item>
                  <v-text-field dense filled clearable prepend-inner-icon="mdi-pencil" label="ワード検索"
                    hint="氏名・所属のから指定したワードを含む項目に絞り込みます。" persistent-hint name="word" v-model="form.word"
                    :error="Boolean(form.errors.word)" :error-messages="form.errors.word"
                    :autofocus="!$vuetify.breakpoint.xs"></v-text-field>
                </v-list-item>


                <v-list-item class="mt-10">
                  <v-select dense filled clearable prepend-inner-icon="mdi-pencil" label="担当商材" :items="productList"
                    return-object hint="担当商材の項目を絞り込みます。" persistent-hint name="product" v-model="form.product"
                    :error="Boolean(form.errors.product)" :error-messages="form.errors.product"
                    :autofocus="!$vuetify.breakpoint.xs"></v-select>
                </v-list-item>

              </v-list>
            </v-card-text>

            <v-card-text class="text-center">
              <Button :small="$vuetify.breakpoint.xs" type="submit">

                <v-icon left>
                  mdi-magnify
                </v-icon>
                この条件で検索する
              </Button>
            </v-card-text>
          </form>

          <v-divider></v-divider>

          <v-card-text class="text-right">
            <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="searchDialog = false">
              <v-icon>
                mdi-close
              </v-icon>
              閉じる
            </Button>
          </v-card-text>
        </v-card>
      </v-dialog>

      <div class="d-flex align-center flex-column" v-if="windowSize >= 800">

        <v-row v-if="windowSize >= 800">

          <v-col v-for="user in users" :key="user.id" cols="4" class="mx-auto my-12" max-width="374" min-width="300">
            <Link as="v-list-item" :href="$route('users.show', { id: user.id })" dusk="userShow">
            <v-card class="ma-2 pa-2">

              <v-img :src="`/storage/user/${user.img_file}`" cover width="375" height="250" v-if="user.img_file"
                class="align-center"></v-img>
              <v-img :src="`/storage/user/noimg.jpeg`" cover v-if="!user.img_file" width="375" height="250"
                class="align-center"></v-img>
              <v-card-title style="font-weight: bold;">{{ user['name'] }} ({{ user['name_kana'] }})</v-card-title>
              <v-card-subtitle class="mt-5">
                <v-icon>mdi-email-outline</v-icon> {{ user['email'] }} <br>
                <v-icon>mdi-phone</v-icon> {{ user['tel'] }} <br>
                <v-icon>mdi-account-group</v-icon> {{ user['department'] }}
              </v-card-subtitle>

            </v-card>
            </Link>
          </v-col>

        </v-row>

      </div>

      <v-container>
        <v-row v-if="windowSize < 800" dense>

          <v-col v-for="user in users" :key="user.id" cols="12" class="mx-auto my-3" max-width="120">
            <Link as="v-list-item" :href="$route('users.show', { id: user.id })" dusk="userShow" class="mx-auto">
            <v-card class="ma-2 pa-2">
              <v-img :src="`/storage/user/${user.img_file}`" cover width="375" height="250" v-if="user.img_file"
                class="align-center"></v-img>
              <v-img :src="`/storage/user/noimg.jpeg`" cover v-if="!user.img_file" width="375" height="250"></v-img>
              <v-card-title style="font-weight: bold;">{{ user['name'] }}</v-card-title>
              <v-card-subtitle class="mt-5">
                <v-icon>mdi-email-outline</v-icon> {{ user['email'] }} <br>
                <v-icon>mdi-phone</v-icon> {{ user['tel'] }} <br>
                <v-icon>mdi-account-group</v-icon> {{ user['department'] }}
              </v-card-subtitle>

            </v-card>
            </Link>
          </v-col>

        </v-row>
      </v-container>



      <v-divider></v-divider>
    </v-card>

  </Layout>
</template>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";

export default {
  components: { Layout, Link },

  props: ["users", "form_params", "productList"],



  data() {
    return {

      // 検索ダイアログ
      searchDialog: false,
      form: this.$inertia.form(this.form_params),
      windowSize: window.innerWidth,


      // 設定されている検索条件の件数
      formParamsCount: Object
        .entries(this.form_params)
        .filter(function (param) {
          return param[1] && param[1].length !== 0;
        }).length,
    };
  },


  methods: {

    // 絞り込み実行
    search() {
      this.form
        .transform((data) => {
          // 値が空の要素を削除
          Object.entries(data).forEach((kv) => {
            if (kv[1] === "" || kv[1] === null) {
              delete data[kv[0]];
            }
          });

          return { ...data };
        })
        .get(this.$route('users.index'), {
          onSuccess: () => {
            this.closeSearchDialog();
          },
        });
    },

    // ダイアログを閉じる
    closeSearchDialog() {
      //  ダイアログを閉じる
      this.searchDialog = false;
    },
    handleResize: function () {
      // resizeのたびにこいつが発火するので、ここでやりたいことをやる
      this.windowSize = window.innerWidth;
    }
  },
  mounted: function () {
    window.addEventListener('resize', this.handleResize)
  },
  beforeDestroy: function () {
    window.removeEventListener('resize', this.handleResize)
  }
}
</script>
