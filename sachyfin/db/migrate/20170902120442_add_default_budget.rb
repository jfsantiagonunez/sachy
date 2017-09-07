class AddDefaultBudget < ActiveRecord::Migration
  def change
    change_column :category_accounts, :budget , :decimal, :default => 0
    change_column :categories, :budget, :decimal, :default => 0
    change_column :grupos, :budget, :decimal , :default => 0
  end
end
