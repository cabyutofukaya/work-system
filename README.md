# Laravel Management System

このプロジェクトは、Laravel 9 + PHP 8.3 をベースとした社内向けのWeb管理システムです。以下のような機能を備え、拡張性・保守性に優れた構成を目指しています。

---

## 🧰 使用技術スタック

- **Laravel Framework**: v9.x
- **PHP**: ^8.3
- **Inertia.js** + Vue.js フロントエンド（SPA構成対応）
- **Laravel-Admin**: 管理画面自動生成
- **Fortify / Sanctum**: 認証・APIセキュリティ
- **Telescope / Telescope Toolbar**: 開発用デバッグツール
- **Ziggy**: ルーティング情報のJS側への共有

---

## 📦 セットアップ手順

### 1. クローンと依存関係インストール

```bash
cd work-system
composer install
npm install && npm run dev
php artisan serve
php migrate
cp .env .env
