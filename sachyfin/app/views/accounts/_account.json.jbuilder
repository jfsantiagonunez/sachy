json.extract! account, :id, :account_number, :name, :last_import, :last_transaction, :created_at, :updated_at
json.url account_url(account, format: :json)