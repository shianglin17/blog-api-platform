# Swagger API 文件撰寫規範

## 📁 目錄結構

```
app/Swagger/
├── OpenApi.php                    # 全域配置（Info, Server, Security, Tags）
├── Controller/Api/                # API 端點文件（對應實際 Controller）
│   ├── AuthController.php
│   ├── CategoryController.php
│   └── ArticleController.php
└── Schemas/                       # 共用 Response Schema
    ├── ErrorResponse.php
    └── ValidationErrorResponse.php
```

## 🎯 核心原則

### ⚠️ 最重要的規則
- ✅ **一切以實作為準**：先看實際的 Controller、Service、Test，再寫 Swagger
- ✅ **範例值必須真實**：從測試檔案或實際 API 回應複製，不要自己編造
- ✅ **回應格式必須一致**：Swagger 寫的和實際 API 回傳的必須完全相同
- ✅ **不要臆測**：不確定的欄位、狀態碼、格式，先去看程式碼或測試

### 撰寫流程
1. 查看實際 Controller（`app/Http/Controllers/Api/`）
2. 查看對應的 Service（`app/Services/`）
3. 查看測試檔案（`tests/Feature/`）了解真實回應
4. 根據以上內容撰寫 Swagger 文件

## ✅ 撰寫規則檢查清單

### 1. 檔案結構
- ✅ 每個 Swagger Controller 對應一個實際 Controller
- ✅ 方法名稱與實際 Controller 一致（index, show, store...）
- ✅ 方法必須加上 return type：`public function index(): void {}`
- ✅ 方法內容留空（只寫文件，不寫邏輯）

### 2. 路徑與方法
- ✅ `path` 必須以 `/` 開頭（例：`'/articles'`）
- ✅ 路徑參數用 `{}` 包裹（例：`'/articles/{slug}'`）
- ✅ HTTP 方法對應正確 Attribute：
  - `#[OA\Get(...)]` - 查詢
  - `#[OA\Post(...)]` - 新增/操作
  - `#[OA\Put(...)]` - 完整更新
  - `#[OA\Patch(...)]` - 部分更新
  - `#[OA\Delete(...)]` - 刪除

### 3. 必填欄位
- ✅ `path`：API 路徑
- ✅ `summary`：API 簡短說明（中文）
- ✅ `tags`：分組標籤（必須在 OpenApi.php 中定義）
- ✅ `responses`：至少包含成功狀態碼（200/201）

### 4. 認證設定
- ✅ 需要認證的端點：`security: [['bearerAuth' => []]]`
- ✅ 公開端點（login）：不加 `security` 參數
- ✅ 只需在端點層級設定，不用每個 Response 重複

### 5. 參數定義
- ✅ 路徑參數（path）：`required: true`
- ✅ 查詢參數（query）：`required: false`（除非必填）
- ✅ 分頁參數範例：
  ```php
  parameters: [
      new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 1)),
      new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 15)),
  ]
  ```

### 6. Request Body（POST/PUT/PATCH）
- ✅ 使用 `requestBody: new OA\RequestBody(...)`
- ✅ 標註必填欄位：`required: ['email', 'password']`
- ✅ 提供範例值：`example: 'admin@example.com'`
- ✅ **範例值從測試檔案複製**（例：從 `LoginTest.php` 複製測試用的 email/password）

### 7. Response 定義
- ✅ 所有可能的 HTTP 狀態碼都要寫
- ✅ 標準狀態碼：
  - `200`：成功（GET）
  - `201`：成功建立（POST）
  - `401`：未認證
  - `403`：無權限
  - `404`：不存在
  - `422`：驗證失敗
- ✅ 統一的回應格式（必須和 `ApiResponse` 實際產生的一致）：
  ```php
  properties: [
      new OA\Property(property: 'data', ...),      // 資料
      new OA\Property(property: 'success', type: 'boolean', example: true),
      new OA\Property(property: 'code', type: 'string', example: '0200'),
      new OA\Property(property: 'message', type: 'string', example: '...'),
  ]
  ```

### 8. 分頁回應（必須包含）
- ✅ `data`：資料陣列
- ✅ `meta`：分頁資訊
  - `current_page`, `from`, `last_page`, `per_page`, `to`, `total`
- ✅ `links`：分頁連結
  - `first`, `last`, `prev`, `next`
- ✅ **結構必須和 `ApiResponse::success()` 處理 Paginator 的格式一致**

### 9. 錯誤回應（使用 Schema 引用）
- ✅ 標準錯誤：`ref: '#/components/schemas/ErrorResponse'`
- ✅ 驗證錯誤：`ref: '#/components/schemas/ValidationErrorResponse'`
- ✅ 範例：
  ```php
  new OA\Response(
      response: 401,
      description: '未認證',
      content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
  )
  ```

### 10. 資料型別對應
- ✅ `type: 'integer'` - 整數（id, count）
- ✅ `type: 'string'` - 字串（name, slug, title）
- ✅ `type: 'string', format: 'email'` - Email
- ✅ `type: 'string', format: 'password'` - 密碼
- ✅ `type: 'string', format: 'date-time'` - 時間（created_at）
- ✅ `type: 'boolean'` - 布林值（success）
- ✅ `type: 'array', items: new OA\Items(...)` - 陣列
- ✅ `type: 'object', properties: [...]` - 物件

### 11. 範例值（example）⭐ 重點
- ✅ **所有範例值必須從實際資料來源取得**：
  - 從測試檔案複製（`tests/Feature/`）
  - 從資料庫 Seeder 複製（`database/seeders/`）
  - 從實際 API 回應複製
- ✅ **欄位名稱必須和 Model 的屬性一致**
- ✅ **關聯資料結構必須和實際 eager loading 的結果一致**
- ✅ 數字不加引號：`example: 1`
- ✅ 字串加引號：`example: 'frontend'`
- ✅ **message 內容必須和 Controller 中實際傳入 `ApiResponse` 的一致**

**範例對照**：
```php
// ❌ 錯誤：自己編造
new OA\Property(property: 'slug', type: 'string', example: 'my-article')

// ✅ 正確：從 DatabaseSeeder.php 複製
new OA\Property(property: 'slug', type: 'string', example: 'frontend-1')
```

### 12. 語法注意事項
- ✅ 頂層用：`#[OA\...]`
- ✅ 嵌套用：`new OA\...`
- ✅ 參數賦值：`property: 'value'`（冒號）
- ✅ 陣列用：`[]`（不是 `{}`）
- ✅ 字串統一用單引號：`'value'`

## 🔧 OpenApi.php 設定

### 何時需要修改
- ✅ 新增 API 分組：加新的 `#[OA\Tag(...)]`
- ✅ 變更 API 版本：修改 `version`
- ✅ 新增認證方式：加新的 `#[OA\SecurityScheme(...)]`
- ✅ 修改伺服器位置：修改 `#[OA\Server(url: ...)]`

### 當前配置
```php
#[OA\Info(version: '1.0.0', title: '部落格平台 API')]
#[OA\Server(url: 'http://localhost/api')]
#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', scheme: 'bearer')]
#[OA\Tag(name: 'Auth')]
#[OA\Tag(name: 'Categories')]
#[OA\Tag(name: 'Articles')]
```

## 📝 Schemas 使用時機

### 何時抽成 Schema
- ✅ 被 3+ 個端點重複使用
- ✅ 結構複雜（5+ 個欄位）
- ✅ 需要統一維護的格式

### 當前 Schemas
- `ErrorResponse`：標準錯誤回應（401, 403, 404）
- `ValidationErrorResponse`：驗證錯誤回應（422）

## 🚀 生成與測試

### 生成文件
```bash
php artisan l5-swagger:generate
```

### 查看文件
```
http://localhost/api/documentation
```

### 開發環境自動生成（.env）
```env
L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_UI_PERSIST_AUTHORIZATION=true
```

## ⚠️ 常見錯誤

### 避免這些錯誤
- ❌ 忘記加 `use OpenApi\Attributes as OA;`
- ❌ 路徑忘記加 `/`：`path: 'articles'` → `path: '/articles'`
- ❌ 嵌套元素忘記 `new`：`OA\Property(...)` → `new OA\Property(...)`
- ❌ 用 `=` 賦值：`property="id"` → `property: 'id'`
- ❌ 陣列用 `{}`：`tags={"Auth"}` → `tags: ['Auth']`
- ❌ 方法沒有 return type：`public function index()` → `public function index(): void`
- ❌ 分頁回應忘記 `meta` 和 `links`
- ❌ 錯誤回應直接寫，沒用 Schema 引用
- ❌ **範例值自己編造，沒有對應實際資料**
- ❌ **回應格式和實際 API 不一致**
- ❌ **message 內容和 Controller 中寫的不同**

## 📋 快速檢查表

每次寫完 Swagger 文件，檢查：
1. [ ] 檔案放在正確位置（`app/Swagger/Controller/Api/`）
2. [ ] 方法名稱與實際 Controller 一致
3. [ ] 方法有 `void` return type
4. [ ] 路徑、summary、tags、responses 都有填
5. [ ] 需要認證的加上 `security`
6. [ ] 所有參數都有 `required`、`schema`、`example`
7. [ ] 所有狀態碼都有定義（200, 401, 403, 404, 422）
8. [ ] 分頁 API 有 `meta` 和 `links`
9. [ ] 錯誤回應使用 Schema 引用
10. [ ] 所有欄位都有範例值
11. [ ] **範例值從測試檔案或 Seeder 複製，不是自己編的**
12. [ ] **回應格式和實際 Controller 回傳的一致**
13. [ ] **message 內容和程式碼中的一致**

## 🎯 品質標準

**好的 Swagger 文件**：
- ✅ 簡潔但完整
- ✅ 範例真實可測試（從實際程式碼來）
- ✅ 所有狀態碼都有說明
- ✅ 一致性高（相同類型 API 格式相同）
- ✅ **和實際 API 行為完全一致**

**不好的 Swagger 文件**：
- ❌ 過度詳細的描述（太多雜訊）
- ❌ 缺少狀態碼或範例
- ❌ 格式不一致
- ❌ **範例值不真實（自己編造的）**
- ❌ **和實際 API 回應不符**
