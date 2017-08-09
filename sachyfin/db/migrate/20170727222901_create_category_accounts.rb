class CreateCategoryAccounts < ActiveRecord::Migration
  def change
    create_table :category_accounts do |t|
      t.integer :category_id
      t.integer :account_id
      t.decimal :budget

      t.timestamps null: false
    end
  end
end
