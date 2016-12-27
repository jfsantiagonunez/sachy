json.extract! transaction, :id, :account_id, :transaction_date, :start_saldo, :end_saldo, :amount, :description, :created_at, :updated_at
json.url transaction_url(transaction, format: :json)