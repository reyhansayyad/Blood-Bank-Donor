# Blood Bank Donor Management System  
## Project Report (Real-Time Database Based Project)

---

## 1. Introduction

**Blood Bank Donor** is a GUI-based real-time application for managing blood donors and blood requests. It allows donors to register, update their availability, request blood, and view request status. Administrators can manage blood requests (accept/reject/delete) and view donor data. The system uses **MongoDB** for persistent storage and **Firebase** for authentication and real-time features, as per the RTD (Real-Time Database) project guidelines.

The application consists of:
- **Donor-facing**: Login, Registration, Dashboard (profile, availability, blood request, request status, donor search).
- **Admin-facing**: Admin panel (donors list from Firebase, blood requests from MongoDB with Accept/Reject/Delete).
- **Backend**: PHP APIs with MongoDB (CRUD, aggregation) and frontend integration with Firebase Auth and Realtime Database.

---

## 2. Concepts Used from RTD Guidelines

The project is aligned with the **TYIT – Real-Time Database Based Projects** guidelines as follows:

| Guideline | Requirement | Implementation in Project |
|-----------|--------------|----------------------------|
| **Dual stack** | MongoDB for main data storage; Firebase for auth and real-time features | MongoDB stores donors (synced from Firebase), blood requests; Firebase handles login/register and Realtime Database for live donor/availability data. |
| **CRUD operations** | Perform Create, Read, Update, Delete | **Create**: Register donor (Firebase), submit blood request (MongoDB). **Read**: Get donors, get requests, get user requests, dashboard stats. **Update**: Save donor profile (MongoDB), update availability (Firebase + sync), update request status (MongoDB). **Delete**: Delete blood request (admin). |
| **Real-time data sync** | Real-time synchronization | Firebase Realtime Database for donors; `onValue` listeners on dashboard and admin for live donor list and availability; donor profile/status sync from Firebase to MongoDB via `save_donor.php`. |
| **Aggregation and indexing** | 1 aggregation pipeline ($match, $group); 1 index with explain() | **Aggregation**: `get_dashboard_stats.php` uses `$match` (available = true) and `$group` (by blood_group, $sum). **Indexing**: Collections can use indexes on frequently queried fields (e.g. `userId`, `id`, `blood_group`); explain() can be used to analyze query performance. |
| **Authentication and security** | Firebase Auth; secure access | Firebase Authentication (Email/Password) for login/register; protected routes (redirect to login if not authenticated); admin APIs can be extended with token/role checks. |
| **Technology** | MongoDB + Firebase + GUI | MongoDB (PHP driver) for `bloodbank_db` (donors, blood_requests); Firebase (Auth + Realtime Database); Frontend: HTML/CSS/JS with Firebase SDK. |
| **GUI screens** | Min 3: Login/Register, Data Entry, Dashboard | **1.** Login (`login.html`) and Registration (`register.html`). **2.** Data Entry: Registration form, blood request form (dashboard), admin donor/request actions. **3.** Dashboard (`dashboard.html`) and Admin Dashboard (`admin/admin.html`) for data display. |
| **MongoDB** | 1 database, 2 collections; CRUD; 1 aggregation; index + explain(); data model | **Database**: `bloodbank_db`. **Collections**: `donors`, `blood_requests`. **CRUD**: As above. **Aggregation**: Dashboard stats pipeline ($match, $group). **Data model**: Referenced model (donors by uid, requests by id/userId); normalized for flexibility and to avoid duplication. |
| **Firebase** | Email/Password Auth; Realtime DB or Firestore; real-time sync; security rules | Email/Password authentication; Realtime Database for `donors`; real-time listeners for donor list and availability; security rules recommended to allow read/write only for authenticated users. |
| **GUI** | Interactive, user-friendly, real-time changes | Buttons and forms for all actions; status messages and table updates after API calls; Firebase `onValue` so donor list and availability update in real time without refresh. |

---

## 3. Objectives

- **Primary**: Build a real-time blood donor and request management system using MongoDB and Firebase as per RTD guidelines.
- **Donor side**: Allow donors to register, log in, maintain profile, mark availability, request blood, and see request status.
- **Admin side**: Let admins view donors (from Firebase) and manage blood requests (from MongoDB) with Accept/Reject/Delete.
- **Technical**: Demonstrate CRUD, aggregation, real-time sync, and a clear separation between MongoDB (persistent/storage) and Firebase (auth + real-time).
- **Usability**: Provide a simple, responsive GUI with at least three screens (Login/Register, Data Entry, Dashboard) that reflect real-time changes.

---

## 4. SWOT Analysis

| | **Internal** | **External** |
|---|--------------|--------------|
| **Positive** | **Strengths** | **Opportunities** |
| | • Dual database design (MongoDB + Firebase) meets RTD requirements. | • Can add more screens (e.g. analytics, reports) using the same aggregation/indexing approach. |
| | • CRUD and aggregation implemented; pipeline documented. | • Firebase security rules and admin auth can be strengthened for production. |
| | • Real-time donor/availability updates via Firebase. | • Mobile or React frontend could reuse the same APIs. |
| | • Clear API layer (PHP) for MongoDB; JSON for frontend–backend communication. | • Indexes and explain() can be extended to more collections and queries. |
| **Negative** | **Weaknesses** | **Threats** |
| | • Admin panel currently has no separate auth (relies on Firebase; admin role can be added). | • Dependency on Firebase and MongoDB availability and quotas. |
| | • Some legacy data (e.g. requests without string `id`) handled with fallback logic. | • Security and scalability need attention for production deployment. |
| | • Dashboard stats API (`get_dashboard_stats.php`) exists but is not yet wired in the UI. | • Browser and PHP/MongoDB environment must be correctly configured (e.g. MongoDB extension). |

---

## 5. Conclusion

The **Blood Bank Donor** project fulfils the RTD guidelines by combining **MongoDB** (main storage, CRUD, aggregation, indexing) with **Firebase** (authentication and real-time database). The application provides the required minimum of three GUI screens—Login/Registration, Data Entry (registration and blood request), and Dashboard (donor and admin)—with real-time updates and a documented aggregation pipeline. Objectives of donor and request management are met; the SWOT analysis highlights strengths in design and technology choice, and opportunities for hardening security and expanding features. With minor enhancements (admin auth, wiring dashboard stats, security rules), the system is well aligned with the RTD concept and suitable for submission as a real-time database–based project.
