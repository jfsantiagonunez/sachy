# encoding: UTF-8
# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your
# database schema. If you need to create the application database on another
# system, you should be using db:schema:load, not running all the migrations
# from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended that you check this file into your version control system.

ActiveRecord::Schema.define(version: 20170831211510) do

  create_table "accounts", force: :cascade do |t|
    t.string   "account_number",     limit: 255
    t.string   "name",               limit: 255
    t.datetime "last_import"
    t.date     "last_transaction"
    t.datetime "created_at",                     null: false
    t.datetime "updated_at",                     null: false
    t.integer  "last_amount_import", limit: 4
  end

  create_table "categories", force: :cascade do |t|
    t.string   "name",       limit: 255
    t.datetime "created_at",                                                 null: false
    t.datetime "updated_at",                                                 null: false
    t.integer  "grupo_id",   limit: 4
    t.decimal  "budget",                 precision: 8, scale: 2
    t.integer  "ctype",      limit: 4,                           default: 0
  end

  create_table "category_accounts", force: :cascade do |t|
    t.integer  "category_id", limit: 4
    t.integer  "account_id",  limit: 4
    t.decimal  "budget",                precision: 10
    t.datetime "created_at",                           null: false
    t.datetime "updated_at",                           null: false
  end

  create_table "grupos", force: :cascade do |t|
    t.string   "name",       limit: 255
    t.datetime "created_at",                                     null: false
    t.datetime "updated_at",                                     null: false
    t.decimal  "budget",                 precision: 8, scale: 2
  end

  create_table "rules", force: :cascade do |t|
    t.string   "name",        limit: 255
    t.integer  "category_id", limit: 4
    t.datetime "created_at",              null: false
    t.datetime "updated_at",              null: false
  end

  create_table "transactions", force: :cascade do |t|
    t.integer  "account_id",       limit: 4
    t.date     "transaction_date"
    t.decimal  "start_saldo",                    precision: 8, scale: 2
    t.decimal  "end_saldo",                      precision: 8, scale: 2
    t.decimal  "amount",                         precision: 8, scale: 2
    t.text     "description",      limit: 65535
    t.datetime "created_at",                                             null: false
    t.datetime "updated_at",                                             null: false
    t.integer  "category_id",      limit: 4
    t.string   "note",             limit: 255
  end

end
