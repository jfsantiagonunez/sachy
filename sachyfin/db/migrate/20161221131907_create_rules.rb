class CreateRules < ActiveRecord::Migration
  def change
    create_table :rules do |t|
      t.string :name
      t.integer :category_id

      t.timestamps null: false
    end
    remove_column :categories, :rule
  end
end
