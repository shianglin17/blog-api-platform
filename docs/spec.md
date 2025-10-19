# 部落格平台 API, filament 後台
---

1. 使用 l5-swaggerAPI 寫文件，openAPI 註解寫在 app/Swagger/Api/{對應的 Controller 名稱}
2. CI/CD 走 GitHub Actions 要跑過給前端用的 api test，推到 release 觸發
3. dockerfile build 以 frankenphp 為 php runtime 為底的 image，使用 ocatne 的參數設定 port, woker
4. controller 處理流程、service 處理邏輯、request 處理驗證、ApiResponse 寫統一回傳(錯誤可以在 boostrape/app.php 統一呼叫)、model 寫關聯，如果有資料邏輯看要寫 servcie 還是 model (重複查詢可以使用 model 的 scope 查詢，不使用 repository)

---
## 需求

### 管理員權限(使用 filament 後台)：
 - 管理員可以：
   - category CRUD
   - article CRUD
   - user CRUD, 賦予角色
   - role CRUD, 賦予角色「文章」權限（後台一樣要做分類/文章的階層式 checkbox，勾選分類，觸發文章批量勾選）

### 一般使用者權限(使用 api)：
 - 認證 *4
    - login, refresh token, logout, profile
 - 分類 *2
   - list(只顯示該分類下有權限文章的分類，分頁)
   - show(分類詳細資訊，無權限文章的分類視為不存在 404)
 - 文章 *2
   - list(只顯示有權限的文章，支援分類過濾，分頁)
   - show(文章詳情含 content，需權限檢查)
 - **所有 API 都需要 token 認證(除了 login 和 refresh-token)**

---
## 資料需求
### 資料庫
 - categories
   - id, slug(unique), name, description, timestamps()
 - articles
   - id, slug(unique), title, content, description, category_id, timestamps()
 - **refresh_tokens**
   - `id`
   - `user_id` (foreignId, indexed): 關聯到 `users` 表。
   - `token` (string, unique): 儲存雜湊後的 refresh token。
   - `expires_at` (timestamp): 過期時間。
   - `timestamps()`
 - role, user, token, permission... 等資料表給套件建立

### Model(在後台建立相對應 filament resource)
 - Category
 - Article (關連到 category)
 - User (HasRole trait)
 - Role (設定該角色可以看到的前台分類、文章，預設角色：`admin`, `normal`, `silver`, `gold`)
 - Filament Role Resource 需支援階層式勾選分類/文章權限，勾分類時批量賦予其底下文章權限
 - **後台權限**：只有 `admin` 角色可以登入 Filament 後台

### migration, seeder
 - migration
   - sanctum, spatie/laravel-permission 套件的 migration 先跑
   - category, article, refresh_tokens
 - seeder
   - 建立 CategorySeeder, ArticleSeeder, RolePermissionSeeder, UserSeeder, DatabaseSeeder
   - **測試資料規劃**：
     - 3個分類：前端、後端、生活
     - 每個分類3篇文章，共9篇
     - 4個角色對應權限：
       - `normal`: 可看1個分類的所有文章（前端）
       - `silver`: 可看2個分類的所有文章（前端、後端）
       - `gold`: 可看3個分類的所有文章（前端、後端、生活）
       - `admin`: 可看3個分類的所有文章 + 後台管理權限
   - **執行順序**：
     1. CategorySeeder（建立分類）
     2. ArticleSeeder（建立文章，依賴 Category）
     3. RolePermissionSeeder（建立角色、權限，並賦予角色對應的文章權限）
     4. UserSeeder（建立用戶，每個 Role 建立一個對應的 User）
   - DatabaseSeeder::call([CategorySeeder, ArticleSeeder, RolePermissionSeeder, UserSeeder])
---
## 後台使用 filament 指令建立 filament Resource
 - Resource: Categories, Articles, Users, Roles
 - 結構
   - pages: create, list, edit, delete
   - schemas
   - tables

---
## API
```json
{   
    // 成功回應
    "data":[],
    "code": 0200,
    "success": true,
    "message": "成功"
}
```
```json
{   
    // 失敗回應
    "code": 0404,
    "success": false,
    "message": "找不到頁面"
}
```
### 認證
 - /api/login (發 refresh token, personal_access_token)
 - /api/refresh-token (需要 refresh token)
 - /api/profile (personal_access_token)
 - /api/logout (personal_access_token)
### 分類
 - /api/categories (personal_access_token, 分頁) - 返回有權限文章的分類列表 + accessible_articles_count
 - /api/categories/{slug} (personal_access_token) - 返回分類詳細資訊（description 等）
### 文章
 - /api/articles?category={slug} (personal_access_token, 分頁) - 返回有權限的文章列表，可選分類過濾
 - /api/articles/{slug} (personal_access_token) - 返回文章詳情（含 content）

---
## 權限實作
 - 以最細權限為主（文章）
 - **在 Filament Article Resource 的 hook 處理權限同步**：
   - `afterCreate`: 建立文章時，自動建立對應權限 `article.{slug}`
   - `afterUpdate`: 更新文章 slug 時，刪除舊權限，建立新權限
   - `afterDelete`: 刪除文章時，刪除對應權限
 - 以 article.{slug} 作為 permission->name 作為權限命名
 - 建立 User 的時候，也需要給予角色（Role）
 - **後台操作釐清**：在 Filament UI 中，透過分類進行的批量勾選僅為前端操作輔助，最終提交至後端的請求，皆為針對單一文章顆粒度的權限指派。

---
## 認證實作
 - 使用 sanctum，因需要 refresh_token 功能，所以用一張表來記錄 refresh_token

### refresh_token 技術細節
 - 存入資料庫要雜湊
 - 驗證透過確認雜湊值

### 流程

1. login(登入成功回傳 refresh_token, personal_access_token)
2. access_token 登入失敗，打 api/refresh-token，重新給 access-token
3. 被 sanctum middleware 保護的路由都透過 access-token 進行認證
