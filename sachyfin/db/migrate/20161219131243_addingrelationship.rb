class Addingrelationship < ActiveRecord::Migration
  def change
    add_column :categories, :grupo_id, :integer
    add_column :transactions, :category_id, :integer
  end
end
