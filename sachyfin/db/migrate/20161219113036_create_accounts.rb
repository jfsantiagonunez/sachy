class CreateAccounts < ActiveRecord::Migration
  def change
    create_table :accounts do |t|
      t.string :account_number
      t.string :name
      t.datetime :last_import
      t.datetime :last_transaction

      t.timestamps null: false
    end
  end
end
