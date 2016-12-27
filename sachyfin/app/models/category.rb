class Category < ActiveRecord::Base
  belongs_to :grupo
  has_many :rules
end
