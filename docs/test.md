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
- **測試檔案組織** - 一個 API 一個測試檔案，使用模組分類（資料夾）

---

## 模組 1：認證 (Feature/Auth/)

### LoginTest.php - POST /api/login
**功能**：用戶登入獲取 token

測試案例：
- [x] 正確憑據可以登入（200，返回 access_token, refresh_token, token_type）
- [x] 錯誤密碼無法登入（401，返回錯誤訊息）
- [x] 不存在的信箱無法登入（401）
- [x] 缺少必填欄位返回驗證錯誤（422）

### ProfileTest.php - GET /api/profile
**功能**：獲取當前用戶資料

測試案例：
- [x] 認證用戶可以獲取個人資料（200，返回 name, email, role）
- [x] 未認證用戶無法訪問（401）

### RefreshTokenTest.php - POST /api/refresh-token
**功能**：刷新訪問 token

測試案例：
- [ ] 有效 refresh_token 可以刷新（200，返回新的 access_token）
- [ ] 無效 refresh_token 無法刷新（401）
- [ ] 過期 refresh_token 無法刷新（401）

### LogoutTest.php - POST /api/logout
**功能**：登出並刪除 token

測試案例：
- [x] 認證用戶可以登出（200，refresh_token 和 access_token 從資料庫刪除）
- [x] 未認證用戶無法登出（401）

---

## 模組 2：文章 (Feature/Article/)

### ArticleListTest.php - GET /api/articles?category={slug}
**功能**：獲取用戶有權限的文章列表（分頁，可選分類過濾）

測試案例：
- [ ] normal 角色可以看到 3 篇文章（第一頁顯示全部，因為少於 15 篇）
- [ ] silver 角色可以看到 6 篇文章
- [ ] gold 角色可以看到 9 篇文章
- [ ] 使用 category 參數過濾（normal 查詢 ?category=frontend 返回 3 篇）
- [ ] 分頁結構正確（meta 包含 current_page, last_page, per_page, total）
- [ ] 文章結構正確（id, title, slug, description, category, created_at，不含 content）
- [ ] 未認證用戶無法訪問（401）

### ArticleDetailTest.php - GET /api/articles/{slug}
**功能**：獲取文章詳情

測試案例：
- [ ] 用戶可以查看有權限的文章詳情（200，返回完整資訊含 content 和 category）
- [ ] 用戶無法查看無權限的文章（403）
- [ ] 文章不存在返回 404
- [ ] 未認證用戶無法訪問（401）

---

## 模組 3：分類 (Feature/Category/)

### CategoryListTest.php - GET /api/categories
**功能**：獲取有權限文章的分類列表（分頁，只顯示該分類下有可訪問文章的分類）

測試案例：
- [ ] normal 角色只能看到 1 個分類（前端）
- [ ] silver 角色可以看到 2 個分類（前端、後端）
- [ ] gold 角色可以看到 3 個分類（前端、後端、生活）
- [ ] 分頁結構正確（meta 包含 current_page, last_page, per_page, total）
- [ ] 分類結構正確（id, name, slug, description, accessible_articles_count）
- [ ] accessible_articles_count 計算正確（normal 角色：前端=3）
- [ ] 未認證用戶無法訪問（401）

### CategoryDetailTest.php - GET /api/categories/{slug}
**功能**：獲取分類詳細資訊（不含文章列表）

測試案例：
- [ ] 用戶可以查看有權限的分類詳情（200，返回 id, name, slug, description, timestamps）
- [ ] 用戶無權限的分類返回 404（normal 查看後端分類 → 404，因為沒有該分類文章權限）
- [ ] 分類不存在返回 404
- [ ] 未認證用戶無法訪問（401）

