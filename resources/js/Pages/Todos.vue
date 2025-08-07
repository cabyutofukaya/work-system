<template>
  <Layout>
    <v-card flat tile>
      <v-card-text>
        <v-row>
          <v-col class="text-right">
            <v-btn :small="$vuetify.breakpoint.xs" @click="createDialog = true">
              <v-icon left>mdi-plus</v-icon>
              TODOを作成する
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider class="mx-4" />

      <v-list dense>
        <template v-if="$vuetify.breakpoint.smAndUp">
          <v-list-item>
            <v-list-item-content>
              <v-row class="font-weight-bold">
                <v-col sm="3" class="text-center">日付</v-col>
                <v-col sm="5">内容</v-col>
                <v-col sm="4"></v-col>
              </v-row>
            </v-list-item-content>
          </v-list-item>
        </template>

        <v-divider class="mx-4" />

        <div v-for="todo in todos.data" :key="todo.id">
          <v-list-item>
            <v-list-item-content>
              <v-row :class="{ 'done-row': todo.is_done }">
                <v-col cols="12" sm="3" class="text-center">{{ todo.date }}</v-col>
                <v-col cols="12" sm="5">
                  <span :style="{ textDecoration: todo.is_done ? 'line-through' : 'none' }">
                    {{ todo.title }}
                  </span>
                </v-col>
                <v-col cols="12" sm="4" class="text-right">
                  <v-btn icon color="success" @click="toggleDone(todo)">
                    <v-icon>mdi-check</v-icon>
                  </v-btn>
                  <v-btn icon @click="openEditDialog(todo)">
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>
                  <v-btn icon color="error" @click="deleteTodo(todo.id)">
                    <v-icon>mdi-delete</v-icon>
                  </v-btn>
                </v-col>
              </v-row>
            </v-list-item-content>
          </v-list-item>
          <v-divider class="mx-4" />
        </div>

        <div v-if="!todos.data.length">
          <v-list-item>
            <v-list-item-content>
              <v-list-item-subtitle>TODOはありません。</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </div>
      </v-list>

      <v-card-text>
        <v-pagination v-model="page" :length="length" @input="changePage" />
      </v-card-text>
    </v-card>

    <!-- 編集ダイアログ -->
    <v-dialog v-model="editDialog" max-width="500px">
      <v-card>
        <v-card-title class="headline">TODOを編集</v-card-title>
        <v-card-text>
          <v-text-field v-model="formEdit.date" label="日付" type="date" />
          <v-text-field v-model="formEdit.title" label="TODO" />

        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="editDialog = false">キャンセル</v-btn>
          <v-btn color="primary" :loading="loading.update" @click="submitEdit">保存</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- 作成ダイアログ -->
    <v-dialog v-model="createDialog" max-width="500px">
      <v-card>
        <v-card-title class="headline">TODOを作成</v-card-title>
        <v-card-text>
          <v-text-field v-model="formCreate.date" label="日付" type="date" />
          <v-text-field v-model="formCreate.title" label="TODO" />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="createDialog = false">キャンセル</v-btn>
          <v-btn color="primary" :loading="loading.create" @click="create">保存</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </Layout>
</template>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";
import { Inertia } from "@inertiajs/inertia";

export default {
  components: { Layout, Link },
  props: ['todos'],
  data() {
    return {
      page: Number(this.todos.current_page),
      length: Number(this.todos.last_page),
      createDialog: false,
      editDialog: false,
      formCreate: {
        title: '',
        date: '',
      },
      formEdit: {
        id: null,
        title: '',
        date: '',
        is_done: false,
      },
      loading: {
        create: false,
        update: false,
      },
    };
  },
  methods: {
    create() {
      Inertia.post(this.$route('todos.store'), this.formCreate, {
        preserveState: true,
        onStart: () => (this.loading.create = true),
        onSuccess: () => {
          this.$toasted.show('TODOを作成しました');
          this.createDialog = false;
          this.formCreate.title = '';
          this.formCreate.date = '';
        },
        onFinish: () => (this.loading.create = false),
      });
    },
    openEditDialog(todo) {
      this.formEdit = {
        id: todo.id,
        title: todo.title,
        date: todo.date,
        is_done: todo.is_done,
      };
      this.editDialog = true;
    },
    submitEdit() {
      Inertia.put(this.$route('todos.update', { todo: this.formEdit.id }), this.formEdit, {
        preserveState: true,
        onStart: () => (this.loading.update = true),
        onSuccess: () => {
          this.$toasted.show('TODOを更新しました');
          this.editDialog = false;
        },
        onFinish: () => (this.loading.update = false),
      });
    },
    deleteTodo(id) {
      if (confirm('このTODOを削除しますか？')) {
        Inertia.delete(this.$route('todos.destroy', { todo: id }), {
          onSuccess: () => {
            this.$toasted.show('TODOを削除しました');
          },
        });
      }
    },
    changePage() {
      const url = new URL(this.todos.last_page_url);
      this.page !== 1
        ? url.searchParams.set('page', String(this.page))
        : url.searchParams.delete('page');
      this.$inertia.get(url.href);
    },
    toggleDone(todo) {
      Inertia.patch(this.$route('todos.toggleDone', { todo: todo.id }), {
        onSuccess: () => {
          this.$toasted.show('TODOを完了しました');
        },
      });
    }
  },
};
</script>

<style scoped>
.done-row {
  background-color: #f0f0f0;
  color: #999;
}
</style>
