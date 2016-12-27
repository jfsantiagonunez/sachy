class Addrule < ActiveRecord::Migration
  def change
    add_column :categories, :rule , :string
  end
end
