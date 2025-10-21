# Designer App - Visual Flow Diagram

## 🎯 Main User Journey Flow

```
┌─────────────────┐
│   SPLASH        │ ──────► Check Token ────────► ┌─────────────────┐
│   SCREEN        │                                │   DASHBOARD     │
│   🏢            │ ◄─────── Invalid Token        │   (Main Hub)    │
└─────────────────┘                                │   👋 Welcome    │
         │                                         └─────────────────┘
         │ No Token                                          │
         ▼                                                   │
┌─────────────────┐                                         │
│   LOGIN         │ ──────► Enter Credentials ─────────────►│
│   SCREEN        │                                         │
│   📝            │ ◄─────── Login Failed                   │
└─────────────────┘                                         │
                                                             │
                                    ┌────────────────────────┼────────────────────────┐
                                    │                        │                        │
                                    ▼                        ▼                        ▼
                            ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
                            │   QR SCANNER    │    │   STATISTICS    │    │    PROFILE     │
                            │   📸            │    │   📊            │    │    👤          │
                            └─────────────────┘    └─────────────────┘    └─────────────────┘
                                    │                                                   │
                                    │ QR Detected                                      │ Logout
                                    ▼                                                   ▼
                            ┌─────────────────┐                                ┌─────────────────┐
                            │  SPONSOR        │                                │   LOGIN         │
                            │  VERIFICATION   │                                │   SCREEN        │
                            │  ✅             │                                │   📝            │
                            └─────────────────┘                                └─────────────────┘
                                    │
                                    │ Confirm Visit
                                    ▼
                            ┌─────────────────┐
                            │  VISIT FORM     │ ──────► Capture Photo ────► ┌─────────────────┐
                            │  📝             │                             │   CAMERA        │
                            └─────────────────┘                             │   📸            │
                                    │                                       └─────────────────┘
                                    │ Submit Visit                                   │
                                    ▼                                               │ Photo Taken
                            ┌─────────────────┐ ◄──────────────────────────────────┘
                            │   UPLOAD        │
                            │   STATUS        │ ──────► Success ─────────► ┌─────────────────┐
                            │   📤            │                            │   DASHBOARD     │
                            └─────────────────┘                            │   (Updated)     │
                                    │                                       └─────────────────┘
                                    │ Error
                                    ▼
                            ┌─────────────────┐
                            │   ERROR /       │
                            │   RETRY         │
                            │   ⚠️            │
                            └─────────────────┘
```

## 📱 Screen Navigation Map

```
                                    DASHBOARD (Main Hub)
                                         │
                    ┌────────────────────┼────────────────────┐
                    │                    │                    │
            ┌───────▼─────────┐ ┌────────▼────────┐ ┌────────▼────────┐
            │   QR SCANNER    │ │   STATISTICS    │ │    PROFILE      │
            │      📸         │ │      📊         │ │      👤         │
            └───────┬─────────┘ └─────────────────┘ └────────┬────────┘
                    │                                        │
                    ▼                                        ▼
            ┌───────────────────┐                   ┌────────────────────┐
            │ SPONSOR           │                   │ SETTINGS /         │
            │ VERIFICATION ✅   │                   │ LOGOUT             │
            └───────┬───────────┘                   └────────────────────┘
                    │
                    ▼
            ┌───────────────────┐        ┌────────────────────┐
            │ VISIT FORM 📝     │───────►│ CAMERA 📸          │
            └───────┬───────────┘        └────────┬───────────┘
                    │                             │
                    │                             ▼
                    │                    ┌────────────────────┐
                    │                    │ PHOTO PREVIEW      │
                    │                    │ 🖼️                 │
                    │                    └────────┬───────────┘
                    │                             │
                    ▼◄────────────────────────────┘
            ┌───────────────────┐
            │ SUBMIT VISIT 📤   │
            └───────┬───────────┘
                    │
                    ▼
            ┌───────────────────┐
            │ SUCCESS / ERROR   │
            │ 🎉 / ⚠️           │
            └───────────────────┘

                    BOTTOM NAVIGATION
            ┌─────────┬─────────┬─────────┬─────────┐
            │ SCAN 📸 │HISTORY📚│STATS 📊 │PROFILE👤│
            └─────────┴─────────┴─────────┴─────────┘
                      │         │
                      ▼         ▼
              ┌───────────────────┐ ┌───────────────────┐
              │ VISIT HISTORY     │ │ VISIT DETAILS     │
              │ LIST 📚           │◄┤ VIEW 🔍          │
              └───────┬───────────┘ └───────────────────┘
                      │
                      ▼
              ┌───────────────────┐
              │ SEARCH RESULTS    │
              │ 🔍                │
              └───────────────────┘
```

## 🔄 API Integration Points

```
APP SCREENS                    API ENDPOINTS                    DATA FLOW
───────────                    ─────────────                    ─────────

Login Screen                   POST /api/designer/login         Email/Password
     │                              │                               │
     └──────────────────────────────┴───────────────────────────────┘
                                    │
                                    ▼
                              Store Token Locally
                                    │
                                    ▼
Dashboard Screen               GET /api/designer/stats          Display Stats
     │                              │                               │
     └──────────────────────────────┴───────────────────────────────┘
                                    │
                                    ▼
QR Scanner                    POST /api/designer/verify-qr      QR Code Data
     │                              │                               │
     └──────────────────────────────┴───────────────────────────────┘
                                    │
                                    ▼
Visit Form                    POST /api/designer/visits         Multipart Form
     │                              │                          (Notes + Photo)
     └──────────────────────────────┴───────────────────────────────┘
                                    │
                                    ▼
Visit History                 GET /api/designer/visits          Paginated List
     │                              │                               │
     └──────────────────────────────┴───────────────────────────────┘
                                    │
                                    ▼
Search Function               GET /api/designer/visits/search   Search Query
     │                              │                               │
     └──────────────────────────────┴───────────────────────────────┘
```

## 🎯 State Management Flow

```
                        APP LAUNCH
                             │
                             ▼
                    ┌─────────────────┐
                    │  Check Storage  │
                    │  for Token      │
                    └─────────────────┘
                             │
                ┌────────────┴────────────┐
                │                         │
                ▼ (Token Exists)         ▼ (No Token)
        ┌───────────────┐         ┌─────────────────┐
        │ Validate      │         │ AuthState:      │
        │ Token         │         │ isLoggedIn=false│
        └───────────────┘         └─────────────────┘
                │                          │
                │                          ▼
                │                 ┌─────────────────┐
                │                 │ Show Login      │
                │                 │ Screen          │
                │                 └─────────────────┘
                │
        ┌───────┴─────────┐
        │                 │
        ▼ (Valid)        ▼ (Invalid)
┌───────────────┐  ┌─────────────────┐
│ AuthState:    │  │ Clear Token     │
│ isLoggedIn=   │  │ Show Login      │
│ true          │  └─────────────────┘
└───────────────┘
        │
        ▼
┌───────────────┐
│ Load Dashboard│
│ Data (Stats)  │
└───────────────┘
        │
        ▼
┌───────────────┐
│ UIState:      │
│ currentTab=   │
│ dashboard     │
└───────────────┘
```

## 🔄 Photo Upload Flow

```
VISIT FORM SCREEN          CAMERA SERVICE          API SERVICE
─────────────────          ──────────────          ───────────

Tap "Capture Photo"
        │
        ▼
┌───────────────────┐
│ Request Camera    │ ────────► Initialize Camera
│ Permission        │                   │
└───────────────────┘                   │
        │                               ▼
        │                      ┌─────────────────┐
        │                      │ Camera Ready    │
        │                      │ Show Preview    │
        │                      └─────────────────┘
        │                               │
        │                               ▼
        │                      ┌─────────────────┐
        │ ◄──────────────────  │ Take Photo      │
        │                      │ Return File     │
        │                      └─────────────────┘
        ▼                               │
┌───────────────────┐                   │
│ Show Photo        │ ◄─────────────────┘
│ Preview           │
└───────────────────┘
        │
        │ (User confirms)
        ▼
┌───────────────────┐
│ Add to Visit      │
│ Form Data         │
└───────────────────┘
        │
        │ (User submits visit)
        ▼
┌───────────────────┐                           ┌─────────────────┐
│ Create Multipart  │ ─────────────────────────►│ POST /visits    │
│ Request with      │                           │ with Photo      │
│ Photo File        │                           └─────────────────┘
└───────────────────┘                                   │
        │                                               │
        │                                               ▼
        │                                   ┌─────────────────┐
        │ ◄─────────────────────────────────│ Upload Success  │
        │                                   │ Return Visit ID │
        │                                   └─────────────────┘
        ▼
┌───────────────────┐
│ Show Success      │
│ Message           │
└───────────────────┘
```

## 📊 Data Synchronization Strategy

```
                        OFFLINE STORAGE
                    ┌─────────────────────┐
                    │   LOCAL DATABASE    │
                    │                     │
                    │ ┌─────────────────┐ │
                    │ │ Pending Visits  │ │
                    │ │ Cached History  │ │
                    │ │ Sponsor Data    │ │
                    │ │ User Profile    │ │
                    │ └─────────────────┘ │
                    └─────────────────────┘
                             │
                             │ ◄─► Sync when online
                             │
                             ▼
                    ┌─────────────────────┐
                    │      API SERVER     │
                    │                     │
                    │ ┌─────────────────┐ │
                    │ │ Visits Database │ │
                    │ │ Sponsor Data    │ │
                    │ │ User Accounts   │ │
                    │ │ Statistics      │ │
                    │ └─────────────────┘ │
                    └─────────────────────┘

SYNC TRIGGERS:
├── App Launch (check pending uploads)
├── Network Reconnection (auto-sync)
├── Manual Refresh (pull-to-refresh)
├── Background Sync (periodic)
└── Visit Submission (immediate upload)
```

---

This visual flow document complements the detailed technical documentation and provides your Flutter development team with clear visual guidance for implementing the Designer mobile application. Each diagram shows the relationships between screens, data flow, and technical architecture decisions.