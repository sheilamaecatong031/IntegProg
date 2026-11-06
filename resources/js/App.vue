<script setup>
import { ref, watch, onMounted } from 'vue';
import { useAuth } from './composables/useAuth';
import { useNotify } from './composables/useNotify';
import { useStudents } from './composables/useStudents';
import LoginForm from './components/LoginForm.vue';
import MessageBanner from './components/MessageBanner.vue';
import StudentForm from './components/StudentForm.vue';
import StudentTable from './components/StudentTable.vue';

const { user, isLoggedIn, isAdmin, loggingIn, loginError, login, logout } =
  useAuth();
const { message, notify } = useNotify();
const {
  items: students,
  loading,
  saving,
  deletingId,
  errors,
  load,
  save,
  remove,
  reset,
} = useStudents();

const editing = ref(null);
const showForm = ref(false);

function openAdd() {
  editing.value = null;
  errors.value = {};
  showForm.value = true;
}

function openEdit(student) {
  editing.value = student;
  errors.value = {};
  showForm.value = true;
}

function closeForm() {
  showForm.value = false;
  editing.value = null;
  errors.value = {};
}

async function handleLogin(credentials) {
  const ok = await login(credentials);

  if (ok) {
    notify(`Welcome, ${user.value.name}!`);
    await load();
  }
}

async function handleLogout() {
  await logout();
  notify('Logged out successfully.');
}

async function handleSave(form) {
  const ok = await save(form, editing.value?.id);
  if (ok) closeForm();
}

// When the session ends (logout or expired token), clear the screen state
watch(isLoggedIn, (loggedIn) => {
  if (!loggedIn) {
    reset();
    closeForm();
  }
});

onMounted(() => {
  if (isLoggedIn.value) load();
});
</script>

<template>
  <div class="container">
    <header>
      <h1>Student Management</h1>
      <div v-if="isLoggedIn" class="user-info">
        <span>{{ user.name }}</span>
        <span class="badge">{{ isAdmin ? 'Administrator' : 'Regular User' }}</span>
        <button class="btn" @click="handleLogout">Logout</button>
      </div>
    </header>

    <MessageBanner :text="message.text" :type="message.type" />

    <LoginForm
      v-if="!isLoggedIn"
      :error="loginError"
      :loading="loggingIn"
      @submit="handleLogin"
    />

    <template v-else>
      <p v-if="!isAdmin" class="banner info">
        You are logged in as a Regular User. You can view student records only.
      </p>

      <button
        v-if="isAdmin && !showForm"
        class="btn primary add-btn"
        @click="openAdd"
      >
        + Add Student
      </button>

      <StudentForm
        v-if="showForm"
        :student="editing"
        :errors="errors"
        :saving="saving"
        @save="handleSave"
        @cancel="closeForm"
      />

      <StudentTable
        :students="students"
        :is-admin="isAdmin"
        :loading="loading"
        :deleting-id="deletingId"
        @edit="openEdit"
        @remove="remove"
      />
    </template>
  </div>
</template>