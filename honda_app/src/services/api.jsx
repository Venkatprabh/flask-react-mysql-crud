// import axios from 'axios';

// const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:5173';

// const api = axios.create({
//   baseURL: API_BASE_URL,
//   headers: {
//     'Content-Type': 'application/json',
//   },
// });

// // User API calls
// export const userAPI = {
//   // Get all users
//   getUsers: async () => {
//     const response = await api.get('/User');
//     return response.data; 
//   },
  
//   // Create new user
//   createUser: async (userData) => {
//     const response = await api.post('/User', userData);
//     return response.data;
//   },
  
//   // Update user
//   updateUser: async (id, userData) => {
//     const response = await api.put(`/User/${id}`, userData);
//     return response.data;
//   },
  
//   // Delete user
//   deleteUser: async (id) => {
//     const response = await api.delete(`/User/${id}`);
//     return response.data;
//   },
// };

// export default api;
// -------------------------------------------------------------------------------------------------------------
import axios from "axios";

const PHP_API_URL = import.meta.env.VITE_PHP_API_URL || "http://localhost/php_api/api";
  
const api = axios.create({
  baseURL: PHP_API_URL,
  headers: {
    "Content-Type": "application/json",
  },
});

const phpAPI = {
  // Get all users
  getUsers: async () => {
    const response = await api.get("/users");
    return response.data;
  },

  // Create user
  createUser: async (userData) => {
    const response = await api.post("/users", userData);
    return response.data;
  },

  // Delete user
  deleteUser: async (id) => {
    const response = await api.delete(`/users/${id}`);
    return response.data;
  },
};

export default phpAPI;
