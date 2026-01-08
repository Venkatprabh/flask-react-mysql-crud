// import { useState } from 'react'
// import reactLogo from './assets/react.svg'
// import viteLogo from '/vite.svg'
// import './App.css'

// function App() {
//   const [count, setCount] = useState(0)

//   return (
//     <>
//       <div>
//         <a href="https://vite.dev" target="_blank">
//           <img src={viteLogo} className="logo" alt="Vite logo" />
//         </a>
//         <a href="https://react.dev" target="_blank">
//           <img src={reactLogo} className="logo react" alt="React logo" />
//         </a>
//       </div>
//       <h1>Vite + React</h1>
//       <div className="card">
//         <button onClick={() => setCount((count) => count + 1)}>
//           count is {count}
//         </button>
//         <p>
//           Edit <code>src/App.jsx</code> and save to test HMR
//         </p>
//       </div>
//       <p className="read-the-docs">
//         Click on the Vite and React logos to learn more
//       </p>
//     </>
//   )
// }

// export default App


// ---------------------------------------------------------------------

import { useEffect, useState } from "react";

function App() {
  const [users, setUsers] = useState([]);

  const [name, setName] = useState("");
  const [email, setEmail] = useState("");

  const [editId, setEditId] = useState(null);

  // 1. FETCH USERS
  const fetchUsers = async () => {
    const res = await fetch("http://127.0.0.1:5000/api/users");
    const data = await res.json();
    setUsers(data);
  };

  useEffect(() => {
    fetchUsers();
  }, []);

  // 2. ADD USER
  const addUser = async (e) => {
    e.preventDefault();

    await fetch("http://127.0.0.1:5000/api/users", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name, email }),
    });

    setName("");
    setEmail("");
    fetchUsers();
  };

  // 3. EDIT USER
  const startEdit = (user) => {
    setEditId(user.id);
    setName(user.name);
    setEmail(user.email);
  };

  const updateUser = async (e) => {
    e.preventDefault();

    await fetch(`http://127.0.0.1:5000/api/users/${editId}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name, email }),
    });

    setEditId(null);
    setName("");
    setEmail("");
    fetchUsers();
  };

  // 4. DELETE USER
  const deleteUser = async (id) => {
    await fetch(`http://127.0.0.1:5000/api/users/${id}`, {
      method: "DELETE",
    });

    fetchUsers();
  };

  return (
    <div style={{ padding: "20px" }}>
      <h2>{editId ? "Edit User" : "Add User"}</h2>

      <form onSubmit={editId ? updateUser : addUser}>
        <input
          type="text"
          placeholder="Name"
          value={name}
          onChange={(e) => setName(e.target.value)}
          required
        />

        <input
          type="email"
          placeholder="Email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          required
        />

        <button type="submit">
          {editId ? "Update User" : "Add User"}
        </button>

        {editId && (
          <button type="button" onClick={() => setEditId(null)}>
            Cancel
          </button>
        )}
      </form>

      <hr />

      <h2>User List</h2>

      <ul>
        {users.map((user) => (
          <li key={user.id}>
            {user.name} ({user.email})

            <button onClick={() => startEdit(user)}>Edit</button>
            <button onClick={() => deleteUser(user.id)}>Delete</button>
          </li>
        ))}
      </ul>
    </div>
  );
}

export default App;
