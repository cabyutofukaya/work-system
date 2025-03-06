<template>
  <Layout>
    <div>
      <v-card
          flat tile
      >
        <v-card-title>
          <v-icon dark left v-html="client_type.icon"></v-icon>
          {{ client_type.name }}の追加
        </v-card-title>

        <v-card-text>
          <div class="description-form">
            <v-row>
              <v-col cols="12" sm="4">会社名</v-col>
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
              <v-col cols="12" sm="4">会社の種類</v-col>
              <v-col cols="3">
                <v-select dense filled label="名称の位置"
                prepend-inner-icon="mdi-pencil" :items="name_position" item-value="id"
                item-text="name" clearable v-model="form.name_position" :error="Boolean(form.errors.name_position)"
                :error-messages="form.errors.name_position"></v-select>   
              </v-col>
              <v-col cols="6">
                <v-select dense filled label="会社の種類"
                prepend-inner-icon="mdi-pencil" :items="client_list" item-value="id"
                item-text="name" clearable v-model="form.type_name" :error="Boolean(form.errors.type_name)"
                :error-messages="form.errors.type_name"></v-select>   
              </v-col>

            </v-row>

            <v-row>
              <v-col cols="12" sm="4">会社名よみがな</v-col>
              <v-col>
                <v-text-field
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    name="name_kana"
                    v-model="form.name_kana"
                    maxlength="200"
                    :error="Boolean(form.errors.name_kana)"
                    :error-messages="form.errors.name_kana"
                    
                ></v-text-field>
              </v-col>
            </v-row>


            <v-row>
              <v-col cols="12" sm="4">屋号</v-col>
              <v-col>
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="business_name" v-model="form.business_name"
                  maxlength="200" :error="Boolean(form.errors.business_name)" :error-messages="form.errors.business_name"></v-text-field>
              </v-col>
            </v-row>


            <v-row>
              <v-col cols="12" sm="4">屋号よみがな</v-col>
              <v-col>
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" name="business_name_kana"
                  v-model="form.business_name_kana" maxlength="200" :error="Boolean(form.errors.business_name_kana)"
                  :error-messages="form.errors.business_name_kana"></v-text-field>
              </v-col>
            </v-row>

            <!-- <v-row>
              <v-col cols="12" sm="4">画像</v-col>
              <v-col>
                <v-file-input
                    dense filled
                    prepend-icon=""
                    prepend-inner-icon="mdi-paperclip"
                    v-model="form._image"
                    :error="Boolean(form.errors._image)"
                    :error-messages="form.errors._image"
                ></v-file-input>
              </v-col>
            </v-row> -->

            <v-row>
              <v-col cols="12" sm="4">郵便番号</v-col>
              <v-col>
                <v-text-field
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    hint="半角数字のみ7桁で入力してください" persistent-hint
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
                    name="prefecture"
                    label="都道府県"
                    :items="prefectures"
                    v-model="form.prefecture"
                    :error="Boolean(form.errors.prefecture)"
                    :error-messages="form.errors.prefecture"
                >
                </v-select>
                <v-text-field
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    name="address"
                    label="市区町村以下"
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
                <input type="hidden" name="lat" v-model="form.lat">
                <input type="hidden" name="lng" v-model="form.lng">

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
              <v-col cols="12" sm="4">URL</v-col>
              <v-col>
                <v-text-field
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    name="url"
                    v-model="form.url"
                    maxlength="200"
                    :error="Boolean(form.errors.url)"
                    :error-messages="form.errors.url"
                    
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">メールアドレス</v-col>
              <v-col>
                <v-text-field
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    name="email"
                    v-model="form.email"
                    maxlength="200"
                    :error="Boolean(form.errors.email)"
                    :error-messages="form.errors.email"
                    
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">代表者名</v-col>
              <v-col>
                <v-text-field
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    name="representative"
                    v-model="form.representative"
                    maxlength="200"
                    :error="Boolean(form.errors.representative)"
                    :error-messages="form.errors.representative"
                    
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">代表者カナ</v-col>
              <v-col>
                <v-text-field
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    name="representative_kana"
                    v-model="form.representative_kana"
                    maxlength="200"
                    :error="Boolean(form.errors.representative_kana)"
                    :error-messages="form.errors.representative_kana"
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">代表者役職</v-col>
              <v-col>
                <v-text-field
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    name="representative_position"
                    v-model="form.representative_position"
                    maxlength="200"
                    :error="Boolean(form.errors.representative_position)"
                    :error-messages="form.errors.representative_position"
                ></v-text-field>
              </v-col>
            </v-row>



            <v-row>
              <v-col cols="12" sm="4">代表者携帯番号</v-col>
              <v-col>
                <v-text-field
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    name="representative_tel"
                    v-model="form.representative_tel"
                    maxlength="200"
                    :error="Boolean(form.errors.representative_tel)"
                    :error-messages="form.errors.representative_tel"
                ></v-text-field>
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

            <v-row>
              <v-col cols="12" sm="4">営業時間</v-col>
              <v-col>
                <v-text-field
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    name="business_hours"
                    v-model="form.business_hours"
                    maxlength="200"
                    :error="Boolean(form.errors.business_hours)"
                    :error-messages="form.errors.business_hours"
                    
                ></v-text-field>
              </v-col>
            </v-row>

            <!-- 固有情報 バス・タクシー会社 -->
            <template v-if="client_type['id'] === 'taxibus'">
              <v-row>
                <v-col cols="12" sm="4">会費</v-col>
                <v-col>
                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      hint="半角数字のみで入力してください" persistent-hint
                      name="membership_fee"
                      v-model="form['client_type_taxibus']['membership_fee']"
                      maxlength="200"
                      :error="Boolean(form.errors['client_type_taxibus.membership_fee'])"
                      :error-messages="form.errors['client_type_taxibus.membership_fee']"
                      
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">手数料 タクシー CAB</v-col>
                <v-col>
                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      hint="半角数字のみで入力してください" persistent-hint
                      name="fee_taxi_cab"
                      v-model="form['client_type_taxibus']['fee_taxi_cab']"
                      maxlength="200"
                      :error="Boolean(form.errors['client_type_taxibus.fee_taxi_cab'])"
                      :error-messages="form.errors['client_type_taxibus.fee_taxi_cab']"
                      
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">手数料 タクシー たびの足</v-col>
                <v-col>
                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      hint="半角数字のみで入力してください" persistent-hint
                      name="fee_taxi_tabinoashi"
                      v-model="form['client_type_taxibus']['fee_taxi_tabinoashi']"
                      :error="Boolean(form.errors['client_type_taxibus.fee_taxi_tabinoashi'])"
                      :error-messages="form.errors['client_type_taxibus.fee_taxi_tabinoashi']"
                      
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">手数料 バス CAB</v-col>
                <v-col>
                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      hint="半角数字のみで入力してください" persistent-hint
                      name="fee_bus_cab"
                      v-model="form['client_type_taxibus']['fee_bus_cab']"
                      :error="Boolean(form.errors['client_type_taxibus.fee_bus_cab'])"
                      :error-messages="form.errors['client_type_taxibus.fee_bus_cab']"
                      
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">手数料 バス たびの足</v-col>
                <v-col>
                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      hint="半角数字のみで入力してください" persistent-hint
                      name="fee_bus_tabinoashi"
                      v-model="form['client_type_taxibus']['fee_bus_tabinoashi']"
                      :error="Boolean(form.errors['client_type_taxibus.fee_bus_tabinoashi'])"
                      :error-messages="form.errors['client_type_taxibus.fee_bus_tabinoashi']"
                      
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">カテゴリー</v-col>
                <v-col>
                  <v-radio-group
                      dense mandatory row
                      class="ma-0 pa-0"
                      name="category"
                      v-model="form['client_type_taxibus']['category']"
                      :error="Boolean(form.errors['client_type_taxibus.category'])"
                      :error-messages="form.errors['client_type_taxibus.category']"
                  >
                    <v-radio
                        v-for="(name, type) in client_type['categories']"
                        :key="type"
                        :label="name"
                        :value="type"
                    ></v-radio>
                  </v-radio-group>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">DR</v-col>
                <v-col>
                  <div>
                    <template v-for="key in ['has_dr_sightseeing', 'has_dr_female']">
                      <v-switch
                          dense
                          class="d-inline-block mr-8 pa-0"
                          v-model="form['client_type_taxibus'][key]"
                          :label="client_type_column_names[key]"
                      ></v-switch>
                    </template>
                  </div>

                  <div>
                    <template v-for="key in ['has_dr_language_english', 'has_dr_language_chinese', 'has_dr_language_korean', 'has_dr_language_other']">
                      <v-switch
                          dense
                          class="d-inline-block mr-8 pa-0"
                          v-model="form['client_type_taxibus'][key]"
                          :label="client_type_column_names[key].replace('外国語DR ', '') + 'DR'"
                      ></v-switch>
                    </template>
                  </div>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">{{ client_type_column_names['has_wheelchair'] }}</v-col>
                <v-col>
                  <v-switch
                      dense
                      class="ma-0 pa-0"
                      v-model="form['client_type_taxibus']['has_wheelchair']"
                      :label="(form['client_type_taxibus']['has_wheelchair']) ? 'あり' : 'なし'"
                  ></v-switch>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">{{ client_type_column_names['has_baby_seat'] }}</v-col>
                <v-col>
                  <v-switch
                      dense
                      class="ma-0 pa-0"
                      v-model="form['client_type_taxibus']['has_baby_seat']"
                      :label="(form['client_type_taxibus']['has_baby_seat']) ? 'あり' : 'なし'"
                  ></v-switch>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">{{ client_type_column_names['has_child_seat'] }}</v-col>
                <v-col>
                  <v-switch
                      dense
                      class="ma-0 pa-0"
                      v-model="form['client_type_taxibus']['has_child_seat']"
                      :label="(form['client_type_taxibus']['has_child_seat']) ? 'あり' : 'なし'"
                  ></v-switch>

                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      label="料金"
                      hint="半角数字のみで入力してください" persistent-hint
                      name="fee_child_seat"
                      v-model="form['client_type_taxibus']['fee_child_seat']"
                      :disabled="!Boolean(form['client_type_taxibus']['has_child_seat'])"
                      :error="Boolean(form.errors['client_type_taxibus.fee_child_seat'])"
                      :error-messages="form.errors['client_type_taxibus.fee_child_seat']"
                      
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">{{ client_type_column_names['has_junior_seat'] }}</v-col>
                <v-col>
                  <v-switch
                      dense
                      class="ma-0 pa-0"
                      v-model="form['client_type_taxibus']['has_junior_seat']"
                      :label="(form['client_type_taxibus']['has_junior_seat']) ? 'あり' : 'なし'"
                  ></v-switch>

                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      label="料金"
                      hint="半角数字のみで入力してください" persistent-hint
                      name="fee_junior_seat"
                      v-model="form['client_type_taxibus']['fee_junior_seat']"
                      :disabled="!Boolean(form['client_type_taxibus']['has_junior_seat'])"
                      :error="Boolean(form.errors['client_type_taxibus.fee_junior_seat'])"
                      :error-messages="form.errors['client_type_taxibus.fee_junior_seat']"
                      
                  ></v-text-field>

                </v-col>
              </v-row>

              <template v-for="key in ['is_bus_association_member', 'has_safety_mark']">
                <v-row>
                  <v-col cols="12" sm="4">{{ client_type_column_names[key] }}</v-col>
                  <v-col>
                    <v-switch
                        dense
                        class="ma-0 pa-0"
                        v-model="form['client_type_taxibus'][key]"
                        :label="(form['client_type_taxibus'][key]) ? 'はい' : 'いいえ'"
                    ></v-switch>
                  </v-col>
                </v-row>
              </template>
            </template>

            <!-- 固有情報 トラック会社 -->
            <template v-if="client_type['id'] === 'truck'">
              <v-row>
                <v-col cols="12" sm="4">ドライバー数</v-col>
                <v-col>
                  <v-select
                      dense clearable
                      name="drivers_count"
                      v-model="form['client_type_truck']['drivers_count']"
                      :items="client_type['drivers_count']"
                      item-value="name"
                      item-text="name"
                  ></v-select>
                </v-col>
              </v-row>
            </template>

            <!-- 固有情報 飲食店会社 -->
            <template v-if="client_type['id'] === 'restaurant'">
              <v-row>
                <v-col cols="12" sm="4">言語</v-col>
                <v-col>
                  <v-select
                      dense multiple chips
                      hint="複数の言語を設定できます" persistent-hint
                      name="languages"
                      v-model="form['client_type_restaurant']['languages']"
                      :items="client_type['languages']"
                      item-value="name"
                      item-text="name"
                  ></v-select>
                </v-col>
              </v-row>
            </template>

            <!-- 固有情報 旅行業者 -->
            <template v-if="client_type['id'] === 'travel'">
              <v-row>
                <v-col cols="12" sm="4">支払い方法</v-col>
                <v-col>
                  <v-radio-group
                      dense mandatory row
                      class="ma-0 pa-0"
                      name="category"
                      v-model="form['client_type_travel']['payment_method']"
                      :error="Boolean(form.errors['client_type_travel.payment_method'])"
                      :error-messages="form.errors['client_type_travel.payment_method']"
                  >
                    <v-radio
                        v-for="(name, type) in client_type['payment_methods']"
                        :key="type"
                        :label="name"
                        :value="name"
                    ></v-radio>
                  </v-radio-group>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">旅行業登録番号</v-col>
                <v-col>
                  <v-text-field
                      dense filled
                      prepend-inner-icon="mdi-pencil"
                      name="registration_number"
                      v-model="form['client_type_travel']['registration_number']"
                      maxlength="200"
                      :error="Boolean(form.errors['client_type_travel.registration_number'])"
                      :error-messages="form.errors['client_type_travel.registration_number']"
                      
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="4">JATA/ANTA/その他</v-col>
                <v-col>
                  <v-radio-group
                      dense mandatory row
                      class="ma-0 pa-0"
                      name="category"
                      v-model="form['client_type_travel']['group']"
                      :error="Boolean(form.errors['client_type_travel.group'])"
                      :error-messages="form.errors['client_type_travel.group']"
                  >
                    <v-radio
                        v-for="(name, type) in client_type['groups']"
                        :key="type"
                        :label="name"
                        :value="name"
                    ></v-radio>
                  </v-radio-group>
                </v-col>
              </v-row>

            </template>


            <v-row v-if="client_type['id'] === 'travel'">
              <v-col cols="12" sm="4">問い合わせ経路</v-col>
              <v-col>
                <v-select
                    dense
                    persistent-hint
                    name="contact"
                    v-model="form['contact']"
                    :items="['web問い合わせ','メール問い合わせ','電話問い合わせ','fax問い合わせ','その他']"
                ></v-select>
              </v-col>
            </v-row>


            <v-row>
              <v-col cols="12" sm="4">ジャンル</v-col>
              <v-col>
                <v-select
                    dense multiple chips
                    hint="複数のジャンルを設定できます" persistent-hint
                    name="genre_ids"
                    v-model="form['genre_ids']"
                    :items="genres"
                    item-value="id"
                    item-text="name"
                ></v-select>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">商材</v-col>
              <v-col>
                <v-select
                    dense multiple chips
                    hint="複数の商材を設定できます" persistent-hint
                    name="product_ids"
                    v-model="form['product_ids']"
                    :items="products"
                    item-value="id"
                    item-text="name"
                ></v-select>
              </v-col>
            </v-row>

            <v-row v-if="client_type['id'] === 'truck'">
              <v-col cols="12" sm="4">社内担当者</v-col>
              <v-col>
                <v-select
                    dense multiple chips
                    hint="複数の社内担当者を設定できます" persistent-hint
                    name="charge_ids"
                    v-model="form['charge_ids']"
                    :items="charges"
                    item-value="id"
                    item-text="name"
                ></v-select>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">説明</v-col>
              <v-col>
                <v-textarea
                    dense filled
                    prepend-inner-icon="mdi-pencil"
                    name="description"
                    v-model="form.description"
                    :error="Boolean(form.errors.description)"
                    :error-messages="form.errors.description"
                ></v-textarea>
              </v-col>
            </v-row>
          </div>

          <v-col class="text-right">
            <Link as="Button"
                  color="primary"
                  :small="$vuetify.breakpoint.xs"
                  @click.native="create"
                  :loading="loading['create']"
            >
              <v-icon left>
                mdi-content-save-outline
              </v-icon>
              この内容で作成する
            </Link>
          </v-col>
        </v-card-text>

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

  props: ['prefectures', 'client_type', 'form_columns', 'genres', 'products', 'client_type_column_names', 'location_default','charges','client_list','name_position'],

  data() {
    return {
      // フォームヘルパ
      form: this.$inertia.form('ClientsCreate', {
        _image: null,
        ...this.form_columns,
        // 位置情報の初期値
        lat: this.location_default["lat"],
        lng: this.location_default["lng"],
        genre_ids: [],
        product_ids: [],
        charge_ids: [],
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

    // 会社情報作成
    create: function () {
      this.form.post(this.$route('client-types.clients.store', {client_type: this.client_type.id}), {
        onStart: () => this.$set(this.loading, "create", true),
        onSuccess: () => this.$toasted.show('会社情報の作成を完了しました'),
        onFinish: () => this.$set(this.loading, "create", false),
      })
    }
  }
}
</script>
