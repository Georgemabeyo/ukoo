body, html {
  font-family: 'Segoe UI', sans-serif;
  background: var(--background);
  color: var(--foreground);
  min-height: 100vh;
  margin: 0;
  padding: 0;
  line-height: 1.5;
  display: flex;
  flex-direction: column;
}

.container.tree-container {
  max-width: 960px;
  margin: 2rem auto 4rem;
  padding: 0 1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
}

h2.text-center {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--primary-text-light);
  margin-bottom: 2rem;
  text-align: center;
}

/* Tree structure */

.tree ul {
  position: relative;
  padding-top: 1rem;
  margin: 0;
  display: flex;
  flex-wrap: nowrap;
  justify-content: center;
  gap: 1.5rem; /* smaller gap horizontally */
}

.tree ul ul {
  margin-top: 2.5rem;
  justify-content: flex-start;
  flex-direction: column;
  flex-wrap: nowrap;
  gap: 0.6rem; /* tighter gap vertically */
  padding-left: 2rem;
  border-left: 2px solid var(--border);
  display: none;
}

.tree li {
  list-style-type: none;
  position: relative;
  padding-top: 0.5rem;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  width: 170px; /* narrower width */
  max-width: 170px;
  min-width: 150px;
  box-sizing: border-box;
  gap: 0.5rem;
  /* cursor: pointer; */
}

/* Connectors horizontal using pseudo elements */
.tree li:not(:last-child)::after,
.tree li:not(:first-child)::before {
  content: "";
  position: absolute;
  top: 0;
  width: 50%;
  height: 0.8rem;
  border-top: 1.5px solid var(--border);
  z-index: -1;
}

.tree li:not(:last-child)::after {
  right: 100%;
  border-right: 1.5px solid var(--border);
  border-radius: 0 15px 0 0;
}

.tree li:not(:first-child)::before {
  left: 100%;
  border-left: 1.5px solid var(--border);
  border-radius: 15px 0 0 0;
}

/* Vertical connector from parent to children */
.tree ul ul::before {
  content: "";
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 2.5rem;
  border-left: 1.5px solid var(--border);
  z-index: -1;
}

/* Member block */
.member {
  background-color: var(--card);
  color: var(--card-foreground);
  border: 2px solid var(--border);
  padding: 15px 10px 15px 10px;
  border-radius: 12px;
  text-align: left;
  width: 100%;
  box-shadow: 0 5px 9px rgba(0,0,0,0.1);
  user-select: none;
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  transition: box-shadow 0.3s ease, border-color 0.3s ease;
}

.member.alive {
  background-color: var(--primary-bg-light);
  border-color: var(--primary-text-light);
  color: var(--primary-text-light);
}

.member.deceased {
  background-color: #b0b7ad;
  border-color: #81867f;
  color: #555a51;
}

.member:hover,
.member:focus {
  box-shadow: 0 8px 14px var(--primary-bg-light);
  border-color: var(--primary-bg-light);
  outline: none;
  color: var(--primary-text-light);
}

.member img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--accent);
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.member-name {
  font-weight: 700;
  font-size: 1.1rem;
  margin: 0;
  word-break: break-word;
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Toggle children button */
.view-children-btn {
  background-color: var(--primary-text-light);
  color: var(--primary-bg-light);
  border: none;
  border-radius: 50%;
  width: 28px;
  height: 28px;
  cursor: pointer;
  font-weight: 700;
  font-size: 1rem;
  line-height: 1;
  user-select: none;
  transition: background-color 0.3s, color 0.3s;
}

.view-children-btn:hover,
.view-children-btn:focus {
  background-color: var(--accent);
  color: var(--primary-text-light);
  outline: none;
}

/* Display children block vertically inside parent */
.tree li.open > ul {
  display: flex;
  flex-direction: column;
  margin-top: 1rem;
}

/* Children list */
.children-list {
  width: 100%;
}

/* Buttons container */
.btn-container {
  margin-top: 3rem;
  display: flex;
  gap: 1.5rem;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-custom {
  background-color: var(--primary-bg-light);
  color: var(--accent);
  border: none;
  padding: 0.8rem 2.5rem;
  border-radius: 9999px;
  font-weight: 700;
  cursor: pointer;
  transition: background-color 0.3s, color 0.3s;
  text-decoration: none;
  text-align: center;
}

.btn-custom:hover,
.btn-custom:focus {
  background-color: var(--primary-text-light);
  color: var(--primary-bg-light);
  outline: none;
}

/* Modal styles */
#modal-overlay {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  z-index: 999;
}

#member-modal {
  display: none;
  position: fixed;
  top: 25%;
  left: 50%;
  transform: translateX(-50%);
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 0 20px rgba(0,0,0,0.6);
  max-width: 400px;
  background-color: var(--card);
  color: var(--card-foreground);
  z-index: 1000;
}

#close-modal {
  background: none;
  border: none;
  font-size: 1.5rem;
  font-weight: bold;
  cursor: pointer;
  position: absolute;
  top: 12px;
  right: 18px;
  color: var(--primary-text-light);
  transition: color 0.3s ease;
}

#close-modal:hover,
#close-modal:focus {
  color: var(--primary-bg-light);
  outline: none;
}

/* Dark mode overrides */
body.dark-mode {
  background: oklch(0.145 0 0);
  color: oklch(0.985 0 0);
}

body.dark-mode .member.alive {
  background-color: var(--primary-bg-dark);
  border-color: var(--primary-text-dark);
  color: var(--primary-text-dark);
}

body.dark-mode .member.deceased {
  background-color: #5e645a;
  border-color: #91997f;
  color: #cbcfc3;
}

body.dark-mode #member-modal {
  background-color: var(--primary-bg-dark);
  color: var(--primary-text-dark);
  box-shadow: 0 0 15px rgba(0,0,0,0.9);
}

body.dark-mode #close-modal {
  color: var(--primary-text-dark);
}

/* Responsive */
@media (max-width: 768px) {
  .tree ul,
  .tree ul ul {
    flex-direction: column;
    gap: 1.5rem;
  }
  .tree li {
    max-width: 90vw !important;
    width: 100% !important;
    padding: 0.75rem 0 0 0;
  }
  .btn-container {
    flex-direction: column;
    gap: 1rem;
  }
}
