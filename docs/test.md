# 測試文件

## 重要原則
- `RefreshDatabase()` - 每個測試使用獨立的資料庫
- 資料準備 trait - 重用測試資料創建邏輯
- 描述性方法名稱，如 `test_user_can_view_article_with_permission()`
- AAA，Arrange（準備）, Act（執行）, Assert（斷言）
- 使用 factory，`Article::factory()->create()`
- CI 要跑過（測試通過）
- 先列測試案例
- 測試隔離 - 每個測試獨立運行，不依賴其他測試
- 完整 assert - 檢查狀態碼 + JSON 結構 + 資料內容
- 測試正反兩面 - 成功場景 + 失敗場景（401, 403, 422 等）

---

## 模組 1：認證 (AuthTest.php)

### API: POST /api/login
**功能**：用戶登入獲取 token

測試案例：
- [ ] 正確憑據可以登入（200，返回 token 和 user 資訊）
- [ ] 錯誤密碼無法登入（401，返回錯誤訊息）
- [ ] 不存在的信箱無法登入（401）
- [ ] 缺少必填欄位返回驗證錯誤（422）

### API: GET /api/profile
**功能**：獲取當前用戶資料

測試案例：
- [ ] 認證用戶可以獲取個人資料（200，返回 name, email, roles）
- [ ] 未認證用戶無法訪問（401）

### API: POST /api/refresh
**功能**：刷新訪問 token

測試案例：
- [ ] 可以刷新 token（200，返回新 token）
- [ ] 無效 token 無法刷新（401）

### API: POST /api/logout
**功能**：登出並刪除 token

測試案例：
- [ ] 可以登出（200，token 從資料庫刪除）
- [ ] 未認證用戶無法登出（401）

---

## 模組 2：文章 (ArticleTest.php)

### API: GET /api/articles
**功能**：獲取用戶有權限的文章列表

測試案例：
- [ ] 用戶可以查看有權限的文章列表（創建 3 篇文章，用戶只有 2 篇權限，只返回 2 篇）
- [ ] 結構測試（包含 id, title, slug, description, category, created_at）
- [ ] 未認證用戶無法訪問（401）
- [ ] 無權限的用戶返回空列表（200，空陣列）

### API: GET /api/articles/{slug}
**功能**：獲取文章詳情

測試案例：
- [ ] 用戶可以查看有權限的文章詳情（200，返回完整文章資訊）
- [ ] 用戶無法查看無權限的文章（403，返回錯誤訊息）
- [ ] 文章不存在返回 404
- [ ] 未認證用戶無法訪問（401）

---

## 模組 3：分類 (CategoryTest.php)

### API: GET /api/categories
**功能**：獲取所有分類及用戶可訪問文章計數

測試案例：
- [ ] 用戶可以查看所有分類（返回所有分類）
- [ ] 結構正確（包含 id, name, slug, accessible_articles_count）
- [ ] 未認證用戶無法訪問（401）

### API: GET /api/categories/{slug}
**功能**：獲取分類詳情及該分類下用戶可訪問的文章

測試案例：
- [ ] 用戶可以查看分類詳情（200，返回分類資訊）
- [ ] 只返回用戶有權限的文章（分類下有 3 篇文章，用戶只有 1 篇權限，只返回 1 篇）
- [ ] 文章列表支持分頁
- [ ] 分類不存在返回 404

---

## 模組 4：權限 (PermissionTest.php)

### 功能：ArticleObserver 權限管理

測試案例：
- [ ] 創建文章時自動創建權限（Article::create(['slug' => 'test',...]) 後，Permission 表中存在 'article.test'）
- [ ] 更新文章 slug 時權限同步更新（slug 從 'old' 更新到 'new'，權限名稱也更新）
- [ ] 刪除文章時權限同步刪除（刪除文章後，對應權限也被刪除）

### 功能：角色和權限分配

測試案例：
- [ ] 可以給角色分配文章權限（$role->givePermissionTo('article.test') 成功）
- [ ] 用戶通過角色繼承權限（用戶分配角色後可以訪問該角色有權限的文章）
- [ ] 可以直接給用戶分配權限（$user->givePermissionTo('article.test') 成功）
