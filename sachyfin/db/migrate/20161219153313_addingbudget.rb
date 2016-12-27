class Addingbudget < ActiveRecord::Migration
  def change
        add_column :categories, :budget, :decimal
        add_column :grupos, :budget, :decimal
  end
end
