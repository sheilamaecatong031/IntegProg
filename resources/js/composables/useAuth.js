import { ref, computed } from 'vue';
import api, { setToken, onUnauthorized } from '../api';
import { useNotify } from './useNotify';

// Shared state (one copy for the whole app)
const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const token = ref(localStorage.getItem('token'));
const loggingIn = ref(false);
const loginError = ref('');

if (token.value) {
  setToken(token.value);
}

function clearSession() {
  setToken(null);
  token.value = null;
  user.value = null;
  localStorage.removeItem('token');
  localStorage.removeItem('user');
}

onUnauthorized(() => {
  if (!token.value) return;

  const { notify } = useNotify();
  clearSession();
  notify('Session expired. Please log in again.', 'error');
});

export function useAuth() {
  const isLoggedIn = computed(() => !!token.value && !!user.value);
  const isAdmin = computed(() => user.value?.role === 'admin');

  async function login(credentials) {
    loggingIn.value = true;
    loginError.value = '';

    try {
      const { data } = await api.post('/login', credentials);

      token.value = data.token;
      user.value = data.user;
      setToken(data.token);
      localStorage.setItem('token', data.token);
      localStorage.setItem('user', JSON.stringify(data.user));

      return true;
    } catch (e) {
      const errs = e.response?.data?.errors;

      loginError.value = errs
        ? Object.values(errs)[0][0]
        : e.response?.data?.message || 'Login failed. Please try again.';

      return false;
    } finally {
      loggingIn.value = false;
    }
  }

  async function logout() {
    try {
      await api.post('/logout');
    } catch (e) {
      // the token may already be invalid; end the session anyway
    }

    clearSession();
  }

  return { user, isLoggedIn, isAdmin, loggingIn, loginError, login, logout };
}