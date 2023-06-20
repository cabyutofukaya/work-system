<template>
  <Layout>
    <v-card flat tile>
      <v-card-title>
        <v-icon dark left>mdi-account</v-icon>
        メンバー
      </v-card-title>

      <v-card-text>
        <v-row>

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


                <!-- <v-list-item class="mt-10">
                  <v-select dense filled clearable prepend-inner-icon="mdi-pencil" label="担当商材"
                 
                  :items="productList"  return-object
                    hint="担当商材の項目を絞り込みます。" persistent-hint
                    name="product" v-model="form.product" :error="Boolean(form.errors.product)" :error-messages="form.errors.product"
                    :autofocus="!$vuetify.breakpoint.xs"></v-select>
                </v-list-item> -->

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

      <v-list>
        <div v-for="user in users" :key="user.id">
          <v-divider class="mx-4"></v-divider>

          <Link as="v-list-item" :href="$route('users.show', { id: user.id })" dusk="userShow">


          <v-list-item-content>
            <v-col cols="2">
              <img :src="`/storage/user/${user.img_file}`" alt="" class="c-img" height="100px" width="150px" style="border-radius: 15%;margin: 0px 10px;" v-if="user.img_file">
              <img :src="`/storage/user/noimg.jpeg`" alt="" class="c-img" height="100px" width="150px" style="border-radius: 15%;margin: 0px 10px;" v-if="!user.img_file">
            </v-col>
            <v-col>
              <v-list-item-title style="font-weight: bold;font-size: 20px;">
                {{ user["name"] }}
              </v-list-item-title>

              <v-list-item-subtitle>
                <v-icon>mdi-email-outline</v-icon>{{ user["email"] }} <span v-if="user.tel">/<v-icon>mdi-phone</v-icon>{{ user["tel"] }}</span> / <v-icon>mdi-account-group </v-icon>所属:{{ user["department"] }}
              </v-list-item-subtitle>
            </v-col>
          </v-list-item-content>

          </Link>
        </div>
      </v-list>

      <v-divider></v-divider>

      <v-card-text class="text-right">
        <BackButton></BackButton>
      </v-card-text>
    </v-card>
  </Layout>
</template>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";

export default {
  components: { Layout, Link },

  props: ["users", "form_params"],

  data() {
    return {

      // 検索ダイアログ
      searchDialog: false,
      form: this.$inertia.form(this.form_params),


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
    }
  }
}
</script>
