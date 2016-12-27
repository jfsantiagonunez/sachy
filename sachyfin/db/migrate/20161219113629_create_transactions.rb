class CreateTransactions < ActiveRecord::Migration
  def change
    create_table :transactions do |t|
      t.integer :account_id
      t.date :transaction_date
      t.decimal :start_saldo
      t.decimal :end_saldo
      t.decimal :amount
      t.string :description

      t.timestamps null: false
    end
  end
end
