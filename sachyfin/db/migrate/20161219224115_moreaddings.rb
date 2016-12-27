class Moreaddings < ActiveRecord::Migration
  def change
    add_column :accounts, :last_amount_import , :integer
    change_column :categories, :budget, :decimal, precision: 8, scale: 2
    change_column :grupos, :budget, :decimal, precision: 8, scale: 2
  end
end
